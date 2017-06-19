<?php

return [

    'groups' => [
        1 => [
            'permissions' => [
                'show' => 1, 'create' => 2, 'edit' => 2, 'delete' => 2, 'all' => 3
            ],
            'areas' => [
                'articles', 'article-categories', 'services', 'pages',
                'photogalleries', 'menu', 'users', 'languages'
            ]
        ]
    ],

];
