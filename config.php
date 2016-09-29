<?php
use Monolog\Logger;

return [
    'db' => [
        'dbhost' => 'localhost',
        'dbuser' => 'root',
        'dbpass' => 'mysql',
        'dbname' => 'vagent'
    ],
    'apigateway' => [
        'timeout' => 10,
        'host'    => '202.176.90.83',
        'user'    => 'robot',
        'pass'    => 'w4J83dmA5MTDDJV6'
    ],
    'config_data_dir' => './conf.d',
    'log_fullpath' => './log/vagent.log',
    'logging_level' => Logger::DEBUG
];
