<?php

return [
    'default_php_version' => [
        'type' => 'anomaly.field_type.select',
        'config' => [
            'handler' => \Visiosoft\ServerModule\Handler\PhpVersions::class,
        ],
        'bind' => 'server::default_php_version'
    ],
];
