<?php

return [

    'status'              => [
        'created'                   => 'Role úspěšně vytvořena',
        'updated'                   => 'Role úspěšně upravena',
        'no-updated'                => 'Role nelze upravovat',
        'deleted'                   => 'Role úspěšně smazána',
        'no-deleted'                => 'Role nelze smazat.',
        'global-enabled'            => 'Role úspěšně aktivována.',
        'global-disabled'           => 'Role úspěšně deaktivována.',
        'enabled'                   => 'Role úspěšně aktivována.',
        'disabled'                  => 'Role úspěšně deaktivována.',
        'no-role-selected'          => 'Nebyla vybrána žádná role.',
    ],

    'error'               => [
        'cant-delete-this-role' => 'Vybraná role nemůže být odstraněna',
        'cant-edit-this-role'   => 'Vybraná role nemůže být editována',
    ],

    'page'              => [
        'index'              => [
            'title'             => 'Admin | Role',
            'description'       => 'List pro role',
            'table-title'       => 'Role pro uživatele',
        ],

        'create'            => [
            'title'            => 'Admin | Role | Vytvořit',
            'description'      => 'Vytvoření nové role',
            'section-title'    => 'Nová role'
        ],
        'edit'              => [
            'title'            => 'Admin | Role | Upravit',
            'description'      => 'Upravení role: :name',
            'section-title'    => 'Editace role'
        ],
    ],

    'columns'           => [
        'id'                        =>  'ID',
        'name'                      =>  'Název',
        'display_name'              =>  'Název role',
        'description'               =>  'Popis',
        'permissions'               =>  'Oprávnění',
        'resync_on_login'           =>  'Re-synchronizovat on login',
        'options'                   =>  'Volby',
        'users'                     =>  'Uživatelů',
        'created'                   =>  'Vytvořeno',
        'updated'                   =>  'Upraveno',
        'actions'                   =>  'Akce',
        'enabled'                   =>  'Aktivace/Deaktivace',
    ],


    'action'               => [
        'create'    =>  'Vytvoření nové role',
    ],

];
