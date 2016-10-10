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
        'phone_number' => 'phone_number', // fixed field
        'source'       => 'source',       // fixed field
        'name'         => 'first_name',   // fixed field
        'email'        => 'email',        // fixed field
        'problem'      => 'Problem',      // custom field 1
        'age'          => 'Age',          // custom field 2
        'height'       => 'Height',       // custom field 3
        'weight'       => 'Weight',       // custom field 4
        'page'         => 'Page',         // custom field 5
        'source'       => 'utm_source',   // custom field 6
        'date_added'   => 'DateAdded',    // custom field 7
        'medium'       => 'Medium',       // custom field 8
        'term'         => 'Term',         // custom field 9
        'content'      => 'Content',      // custom field 10
        'campaign'     => 'Campaign',     // custom field 11
        'gender'       => 'gdr',          // custom field 12
        'id'           => 'id'            // custom field 13
    ],
    'static_fields' => [
        'list_id'       => '1001',
        'phone_code'    => '66',        // mandatory field
        'last_name'     => '',          // not used in 360 source
        'custom_fields' => 'Y',
        'source'        => 'vagent'
    ]
];