<?php

return [

    'status'              => [
        'created'                           => 'Routa úspěšně vytvořena',
        'updated'                           => 'Route úspěšně upravena',
        'deleted'                           => 'Route úspěšně smazána',
        'loaded'                            => 'Úspěšně nahrány :number a smazány :deleted routy z aplikace.',
        'indiv-perms-assigned'              => 'Zvolená routa oprávnění byla upravena.',
        'global-perms-assigned'             => 'Vybrána routa úspěšně přidána.',
        'no-permission-changed-detected'    => 'Žádné změny nebyly provedeny.',
        'global-enabled'                    => 'Vybraná routa je aktivována.',
        'global-disabled'                   => 'Vybraná routa je deaktivována..',
        'enabled'                           => 'Routa byla úspěšně zapnuta.',
        'disabled'                          => 'Routa byla úspěšně vypnuta.',
        'no-route-selected'                 => 'Žádné routy nebyly vybrány.',
    ],

    'error'               => [
        'cant-delete-this-role' => 'Zvolená role nemůže být smazána',
        'cant-edit-this-role'   => 'Zvolená role nemůže být editována',
    ],

    'page'              => [
        'index'              => [
            'title'             => 'Admin | Routy',
            'description'       => 'List všech routů',
            'table-title'       => 'Routy list',
        ],
        'create'            => [
            'title'            => 'Admin | Route | Vytvořit',
            'description'      => 'Vytvoření nové routy',
            'section-title'    => 'Nová routa'
        ],
        'edit'              => [
            'title'            => 'Admin | Route | Upravit',
            'description'      => 'Popis routy',
            'section-title'    => 'Upravit routu'
        ],
    ],

    'columns'           => [
        'id'                        =>  'ID',
        'name'                      =>  'Název',
        'action_name'               =>  'Název v kontroléru',
        'method'                    =>  'Metoda',
        'path'                      =>  'Cesta',
        'created'                   =>  'Vytvořeno',
        'updated'                   =>  'Upraveno',
        'permission'                =>  'Oprávnění',
        'enabled'                   =>  'Aktivace/Deaktivace',
        'desc'                      =>  'Popis',
        'actions'                   =>  'Akce',
    ],

    'action'               => [
        'load-routes'           => 'Obnovit routy k oprávněním',
        'create'                => 'Vytvořit novou routu',
        'save-perms-assignment' => 'Uložit všechny změny v oprávnění',
        'enable-selected'       => 'Zapnout vybrané routy',
        'diable-selected'       => 'Vypnout vybrané routy',
    ],


    'desc'                  => [
        'dashboard'                 => 'Nástěnka',
        'change-password-get'       => 'Změna hesla - get',
        'change-password-post'      => 'Změna hesla - post',
        'switch'                    => 'Přepnutí id webu',

        'ajax'  => [
            'photogallery_list'                 => 'Galerie fotek',
            'photogallery_photo_delete'         => 'Galerie fotek - smazání',
            'photogallery_photo_update'         => 'Galerie fotek - úprava',
            'categories_tree'                   => 'Galerie fotek - strom',
            'upload_photo'                      => 'Galerie fotek - upload',
        ],


        'articles'  => [
            'index'                 => 'Články',
            'store'                 => 'Články - přidání',
            'create'                => 'Články - vytvoření',
            'destroy'               => 'Články - smazání',
            'edit'                  => 'Články - editace',
            'update'                => 'Články - upravení',
        ],

        'categories'  => [
            'index'                 => 'Kategorie',
            'store'                 => 'Kategorie - přidání',
            'create'                => 'Kategorie - vytvoření',
            'delete'                => 'Kategorie - smazání',
            'edit'                  => 'Kategorie - editace',
            'update'                => 'Kategorie - upravení',
        ],

        'menu'  => [
            'index'                 => 'Menu - admin',
            'create'                => 'Vytvořit menu',
            'show'                  => 'Zobrazit',
            'delete'                  => 'Delete',
            'delete-menu-title'     =>'Smazání menu',
            'delete-body'           =>'Opravdu chcete smazat menu?'
        ],

        'users'  => [
            'store'                 => 'Uživatelé - přidání',
            'index'                 => 'Užívatelé',
            'create'                => 'Užívatelé - vytvoření',
            'searchByName'          => 'Užívatelé - vyhledávání',
            'listByPage'            => 'Užívatelé - list stránek',
            'getInfo'               => 'Užívatelé - informace',
            'enableSelected'        => 'Užívatelé - zapnutí výběru',
            'disableSelected'       => 'Užívatelé - vypnutí výběru',
            'show'                  => 'Užívatelé - zobrazení',
            'edit'                  => 'Užívatelé - editace',
            'update'                => 'Užívatelé - upravení',
            'destroy'               => 'Užívatelé - smazání',
            'getModalDelete'        => 'Užívatelé - smazání modal okno',
            'enable'                => 'Užívatelé - aktivace',
            'disable'               => 'Užívatelé - deaktivace',
        ],


        'roles'  => [
            'store'                 => 'Role - přidání',
            'index'                 => 'Role',
            'create'                => 'Role - vytvoření',
            'searchByName'          => 'Role - vyhledávání',
            'listByPage'            => 'Role - list stránek',
            'getInfo'               => 'Role - informace',
            'enableSelected'        => 'Role - zapnutí výběru',
            'disableSelected'       => 'Role - vypnutí výběru',
            'show'                  => 'Role - zobrazení',
            'edit'                  => 'Role - editace',
            'update'                => 'Role - upravení',
            'destroy'               => 'Role - smazání',
            'getModalDelete'        => 'Role - smazání modal okno',
            'enable'                => 'Role - aktivace',
            'disable'               => 'Role - deaktivace',
        ],


        'permissions'  => [
            'index'                 => 'Oprávnění',
            'store'                 => 'Oprávnění - přidání',
            'create'                => 'Oprávnění - vytvoření',
            'generate'              => 'Oprávnění - vyhledávání',
            'enableSelected'        => 'Oprávnění - zapnutí výběru',
            'disableSelected'       => 'Oprávnění - vypnutí výběru',
            'destroy'               => 'Oprávnění - smazání',
            'show'                  => 'Oprávnění - zobrazení',
            'edit'                  => 'Oprávnění - editace',
            'update'                => 'Oprávnění - upravení',
            'getModalDelete'        => 'Oprávnění - smazání modal okno',
            'enable'                => 'Oprávnění - aktivace',
            'disable'               => 'Oprávnění - deaktivace',
        ],


        'routes'  => [
            'index'                 => 'Routy',
            'store'                 => 'Routy - přidání',
            'load'                  => 'Routy - načtení',
            'enableSelected'        => 'Routy - zapnutí výběru',
            'disableSelected'       => 'Routy - vypnutí výběru',
            'savePerms'             => 'Routy - uložení oprávnění',
            'searchByName'          => 'Routy - vyhledání',
            'getInfo'               => 'Routy - zíkání infomace do select',
            'create'                => 'Routy - vytoření',
            'show'                  => 'Routy - zobrazení',
            'edit'                  => 'Routy - editace',
            'update'                => 'Routy - upravení',
            'destroy'               => 'Routy - smazání',
            'getModalDelete'        => 'Routy - smazání modal okno',
            'enable'                => 'Routy - aktivace',
            'disable'               => 'Routy - deaktivace',
        ],




    ],
];
