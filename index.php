<?php
require_once './config.php';
require_once './vendor/autoload.php';

use Ifp\VAgent\Adapter\Source;
use Ifp\VAgent\Adapter\Dest\VicidialDestAdapter;
use Ifp\VAgent\Mapper\Mapper;

use Ifp\Vicidial\VicidialApiGateway;

// open a test connection
$sourceConfig360 = [
    'name' => '360',
    'db' => [
        'dbhost' => '27.254.36.66',
        'dbuser' => '360user',
        'dbpass' => 'Pe2n!*8f',
        'dbname' => 'gt360'
    ],
    'table_name' => 'users',
    'primary_key_field_name' => 'id',
    'select_field_mappings' => [
        // source to dest
        'phone_number' => 'phone_number',
        'source'       => 'source',
        'age'          => 'shoe_size',
        'name'         => 'first_name'
    ],
    'static_fields' => [
        VicidialApiGateway::REQUIRED_PARAM_LIST_ID    => '30000',
        VicidialApiGateway::REQUIRED_PARAM_PHONE_CODE => '66',
        'last_name'     => '',
        'source'        => '360',
        'custom_fields' => 'Y'
    ]
];


try {
    $pdo = new PDO(
        'mysql:host=' . $sourceConfig360['db']['dbhost']
                      . ';dbname=' . $sourceConfig360['db']['dbname'] . ';charset=UTF8',
        $sourceConfig360['db']['dbuser'],
        $sourceConfig360['db']['dbpass']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    var_dump($e->getMessage());
}

//$mock = new Source\MockSourceAdapter();
$source = new Source\MysqlSourceAdapter($pdo, $sourceConfig360);

$apiGateway = new VicidialApiGateway();
$apiGateway->setConnectionTimeoutSeconds($config['apigateway']['timeout'])
           ->setHost($config['apigateway']['host'])
           ->setUser($config['apigateway']['user'])
           ->setPass($config['apigateway']['pass']);

$dest = new VicidialDestAdapter($apiGateway);

$mapper = new Mapper($source, $dest);
$mapper->process();