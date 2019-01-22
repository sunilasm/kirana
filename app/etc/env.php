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
                'dbname' => 'kirana_qa',
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
        'config' => 1,
        'layout' => 1,
        'block_html' => 1,
        'collections' => 1,
        'reflection' => 1,
        'db_ddl' => 1,
        'eav' => 1,
        'customer_notification' => 1,
        'config_integration' => 1,
        'config_integration_api' => 1,
        'full_page' => 1,
        'config_webservice' => 1,
        'translate' => 1,
        'compiled_config' => 1
    ],
    'install' => [
        'date' => 'Tue, 06 Nov 2018 13:45:04 +0000'
    ]
];
