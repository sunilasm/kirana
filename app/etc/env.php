<?php
return [
    'backend' => [
        'frontName' => 'admin_lirbuz'
    ],
    'crypt' => [
        'key' => 'f4d47a20d9933082b718e761c0253a93'
    ],
    'db' => [
        'table_prefix' => 'mg',
        'connection' => [
            'default' => [
                'host' => 'localhost',
                'dbname' => 'kirana_store',
                'username' => 'kirana',
                'password' => 'Kirana@aws123',
                'active' => '1'
            ]
        ]
    ],
    'resource' => [
        'default_setup' => [
            'connection' => 'default'
        ]
    ],
    'x-frame-options' => 'SAMEORIGIN',
    'MAGE_MODE' => 'developer',
    'session' => [
        'save' => 'files'
    ],
    'cache_types' => [
        'config' => 0,
        'layout' => 0,
        'block_html' => 0,
        'collections' => 0,
        'reflection' => 0,
        'db_ddl' => 0,
        'eav' => 0,
        'customer_notification' => 0,
        'config_integration' => 0,
        'config_integration_api' => 0,
        'full_page' => 0,
        'config_webservice' => 0,
        'translate' => 0
    ],
    'install' => [
        'date' => 'Tue, 06 Nov 2018 13:45:04 +0000'
    ]
];
