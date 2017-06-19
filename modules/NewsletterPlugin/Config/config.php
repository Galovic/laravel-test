<?php

return [
    'name' => 'NewsletterPlugin',

    'nickname' => 'newsletterplugin',

    'admin' => [
        'menu' => [
            ['route' => 'admin.module.newsletter_plugin','icon' => 'fa fa-envelope']
        ]
    ],

    'permissions' => [
        'group' => 1
    ],

    'has-page-module' => false

];
