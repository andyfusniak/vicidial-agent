<?php
use Ifp\VAgent\Vicidial\VicidialApiGateway;

return [
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
        'list_id'       => '30000',
        'phone_code'    => '66',
        'last_name'     => '',
        'source'        => '360',
        'custom_fields' => 'Y'
    ]
];