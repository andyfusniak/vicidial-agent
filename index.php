<?php
$timeStart = microtime(true);

require_once './vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Ifp\VAgent\Adapter\Source;
use Ifp\VAgent\Adapter\Dest\VicidialDestAdapter;
use Ifp\VAgent\Mapper\Mapper;
use Ifp\VAgent\Db\DbSync;
use Ifp\VAgent\Version\Version;
use Ifp\Vicidial\VicidialApiGateway;

$config = require_once './config.php';

// change to the project root dir
chdir(__DIR__);

// setup the logging
$logFilepath = str_replace('%date_str%', date('Ymd'), $config['log_fullpath']);
$log = new Logger('vagent');
$log->pushHandler(
    new StreamHandler(
        $logFilepath,
        $config['logging_level']
    )
);

$log->info(sprintf(
    '%s VAgent %s Started %s',
    str_repeat('_', 30),
    Version::VERSION,
    str_repeat('_', 30)
));

// VAgent PDO and DbSync Object
try {
    $pdo = new PDO(
        'mysql:host=' . $config['db']['dbhost']
                      . ';dbname=' . $config['db']['dbname'] . ';charset=UTF8',
        $config['db']['dbuser'],
        $config['db']['dbpass'],
        [
            PDO::ATTR_TIMEOUT => 4,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        ]
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbSync = new DbSync($pdo);
    $dbSync->setLogger($log);
    $log->info(sprintf(
        'Connected to vagent system database (dbhost="%s" dbuser="%s" dbname="%s")',
        $config['db']['dbhost'],
        $config['db']['dbuser'],
        $config['db']['dbname']
    ));
} catch (PDOException $e) {
    $log->critical(sprintf(
        'PDOException: code=%s message="%s" in %s line %s',
        $e->getCode(),
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    ));
    $log->critical(sprintf(
        'PDOException: Trace %s',
        $e->getTraceAsString()
    ));
} catch (Exception $e) {
    $log->critical(sprintf(
        'Exception: code=%s message="%s" in %s line %s',
        $e->getCode(),
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    ));
    $log->critical(sprintf(
        'Exception: Trace %s',
        $e->getTraceAsString()
    ));
}

// read each of the config files from the conf.d directory
// and add each to the source configuration array
$sourceConfig = [];
$glob = glob($config['config_data_dir'] . '/*.php');
$log->debug(sprintf(
    'Scanning "%s" directory for source configs. %s config file(s) found.',
    $config['config_data_dir'],
    count($glob)
));
foreach ($glob as $filepath) {
    $filename = basename($filepath, '.php');
    $sourceConfig[$filename] = include $filepath;
    $log->debug(sprintf(
        'Loaded config file "%s" into $sourceConfig[\'%s\']',
        $filepath,
        $filename
    ));
}

$apiGateway = new VicidialApiGateway();
$apiGateway->setConnectionTimeoutSeconds($config['apigateway']['timeout'])
           ->setHost($config['apigateway']['host'])
           ->setUser($config['apigateway']['user'])
           ->setPass($config['apigateway']['pass']);
$log->info(sprintf(
    'Created VicidialApiGateway (host="%s", user="%s")',
    $config['apigateway']['host'],
    $config['apigateway']['user']
));
$dest = new VicidialDestAdapter($apiGateway);
$dest->setLogger($log);

// process each source config in turn
foreach ($sourceConfig as $source) {
    // Source PDO
    $log->info(sprintf(
        'Attempting to connect to source (dbhost="%s" dbuser="%s" dbname="%s") defined by %s',
        $source['db']['dbhost'],
        $source['db']['dbuser'],
        $source['db']['dbname'],
        $source['name']
    ));
    try {
        $sourcePdo = new PDO(
            'mysql:host=' . $source['db']['dbhost']
                          . ';dbname=' . $source['db']['dbname'] . ';charset=UTF8',
            $source['db']['dbuser'],
            $source['db']['dbpass'],
            [
                PDO::ATTR_TIMEOUT => 4,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
            ]
        );
        $sourcePdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $log->info(sprintf(
            'Connection Established to %s (dbhost="%s" dbuser="%s" dbname="%s")',
            $source['name'],
            $source['db']['dbhost'],
            $source['db']['dbuser'],
            $source['db']['dbname']
        ));

        $sourceAdapter = new Source\MysqlSourceAdapter(
            $sourcePdo,
            $source,
            $config['vagent']
        );
        $sourceAdapter->setLogger($log);

        $mapper = new Mapper(
            $dbSync,
            $sourceAdapter,
            $dest,
            [
                'skip_errors'  => $config['vagent']['skip_errors'],
                'dry_run_mode' => $config['vagent']['dry_run_mode']
            ]
        );
        $mapper->setLogger($log);
        $mapper->process();

        // disconnect from databases
        $sourcePdo = null;
        $log->info(sprintf(
            'Disconnected from source %s (dbhost="%s" dbuser="%s dbname="%s")',
            $source['name'],
            $source['db']['dbhost'],
            $source['db']['dbuser'],
            $source['db']['dbname']
        ));

    } catch (\PDOException $e) {
        $log->critical(sprintf(
            'PDOException: code=%s message="%s" in %s line %s',
            $e->getCode(),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        ));
        $log->critical(sprintf(
            'PDOException: Trace %s',
            $e->getTraceAsString()
        ));
    } catch (Exception $e) {
        $log->critical(sprintf(
            'Exception: code=%s message="%s" in %s line %s',
            $e->getCode(),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        ));
        $log->critical(sprintf(
            'Exception: Trace %s',
            $e->getTraceAsString()
        ));
    }
}

$dbSync->disconnect();
$log->info(sprintf(
        'Disconnected to vagent system database (dbhost="%s" dbuser="%s" dbname="%s")',
        $config['db']['dbhost'],
        $config['db']['dbuser'],
        $config['db']['dbname']
));

$log->info(sprintf(
    '%s VAgent Ending after %s seconds %s',
    str_repeat('_', 30),
    number_format(microtime(true) - $timeStart, 2),
    str_repeat('_', 30)
));
