<?php
use Ifp\VAgent\Vicidial\VicidialApiGateway;

return [
    'name' => 'dis',
    'db' => [
        'dbhost' => 'goldfish.cjk7c0oly6sf.ap-southeast-1.rds.amazonaws.com',
        'dbuser' => 'dis',
        'dbpass' => 'AXW6L42UXXNhb49a',
        'dbname' => 'dis'
    ],
    'table_name' => 'users',
    'primary_key_field_name' => 'id',
    'select_field_mappings' => [
        // source to dest
        'id' => 'id',
        'name'              => 'first_name',   // fixed field
        'phone_number'      => 'phone_number', // fixed field
        'shop_address'      => 'shop_address',
        'preferred_product' => 'preferred_product',
        'facebook'          => 'facebook',
        'instagram'         => 'instagram',
        'linein'            => 'linein',
        'page'              => 'page',
        'source'            => 'utm_source',       // fixed field
        'date_added'        => 'date_added',
        'email'             => 'email',        // fixed field
        'medium'            => 'medium',
        'term'              => 'term',
        'content'           => 'content',
        'campaign'          => 'campaign',
        'others'            => 'others'    
    ],
    'static_fields' => [
        'list_id'       => '7000',
        'phone_code'    => '66',        // mandatory field
        'last_name'     => '',          // not used in 360 source
        'custom_fields' => 'Y',
        'source'        => 'vagent'
    ]
];
