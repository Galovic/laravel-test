<?php

return [
    'name' => 'CareerPlugin',

    'nickname' => 'careerplugin',

    'admin' => [
        'menu' => [
            ['route' => 'admin.module.career.index','icon' => 'fa fa-handshake-o']
        ]
    ],

    'permissions' => [
        'group' => 1
    ]
];
