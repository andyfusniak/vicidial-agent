<?php
use Monolog\Logger;

return [
    'db' => [
        'dbhost' => 'localhost',
        'dbuser' => 'root',
        'dbpass' => 'mysql',
        'dbname' => 'vagent'
    ],
    'vagent' => [
        'default_page_size' => 50,
        'dry_run_mode'      => false,
        'skip_errors'       => true
    ],
    'apigateway' => [
        'timeout' => 10,
        'host'    => 'dialer1.sudtana.com',
        'user'    => 'robot',
        'pass'    => 'w4J83dmA5MTDDJV6',
        'port'    => '8080'
    ],
    'config_data_dir' => './conf.d',
    'log_fullpath'    => './var/logs/%date_str%_vagent.log',
    'logging_level'   => Logger::DEBUG,
];
