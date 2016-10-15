<?php
require_once './vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use Ifp\VAgent\Adapter\Source;
use Ifp\VAgent\Adapter\Dest\VicidialDestAdapter;
use Ifp\VAgent\Mapper\Mapper;
use Ifp\VAgent\Db\DbSync;

use Ifp\Vicidial\VicidialApiGateway;

$config = require_once './config.php';

// change to the project root dir
chdir(__DIR__);


// setup the logging
$log = new Logger('vagent');
$log->pushHandler(new StreamHandler($config['log_fullpath'], $config['logging_level']));
$log->info('VAgent Started');

// read each of the config files from the conf.d directory
// and add each to the source configuration array
$sourceConfig = [];
$glob = glob($config['config_data_dir'] . '/*.php');
$log->debug('Globbing directory "' . $config['config_data_dir']
    . '" found ' . count($glob) . ' file(s)');
foreach ($glob as $filepath) {
    $filename = basename($filepath, '.php');
    $sourceConfig[$filename] = include $filepath;
    $log->debug('Read config file "' . $filepath
        . '" into $sourceConfig[\'' . $filename . '\']');
}


// hard wired for a single source (refactor later)
$sourceConfig = $sourceConfig['dis'];

// Source PDO
$log->info('Attempting to connect to source db defined by '
    . $sourceConfig['name'] . '...');
try {
    $sourcePdo = new PDO(
        'mysql:host=' . $sourceConfig['db']['dbhost']
                      . ';dbname=' . $sourceConfig['db']['dbname'] . ';charset=UTF8',
        $sourceConfig['db']['dbuser'],
        $sourceConfig['db']['dbpass']
    );
    $sourcePdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $log->info('Connection Established');
} catch (PDOException $e) {
    throw $e;
}

//$mock = new Source\MockSourceAdapter();
$source = new Source\MysqlSourceAdapter(
    $sourcePdo,
    $sourceConfig,
    $config['vagent']
);
$source->setLogger($log);

$apiGateway = new VicidialApiGateway();
$apiGateway->setConnectionTimeoutSeconds($config['apigateway']['timeout'])
           ->setHost($config['apigateway']['host'])
           ->setUser($config['apigateway']['user'])
           ->setPass($config['apigateway']['pass']);

$dest = new VicidialDestAdapter($apiGateway);
$dest->setLogger($log);

// VAgent PDO and DbSync Object
try {
    $pdo = new PDO(
        'mysql:host=' . $config['db']['dbhost']
                      . ';dbname=' . $config['db']['dbname'] . ';charset=UTF8',
        $config['db']['dbuser'],
        $config['db']['dbpass']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbSync = new DbSync($pdo);
    $dbSync->setLogger($log);
} catch (PDOException $e) {
    var_dump($e->getMessage());
}

$mapper = new Mapper(
    $dbSync,
    $source,
    $dest,
    [
        'skip_errors'  => true,
        'dry_run_mode' => $config['vagent']['dry_run_mode']
    ]
);
$mapper->setLogger($log);
$mapper->process();

// disconnect from databases
$log->info('Disconnecting from database');
$sourcePdo = null;
$dbSync->disconnect();


$log->info('VAgent Ending');
