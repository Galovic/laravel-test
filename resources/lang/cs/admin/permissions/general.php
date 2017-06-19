<?php

return [

    'status'              => [
        'created'                   => 'Oprávnění úspěšně vytvořeno.',
        'updated'                   => 'Oprávnění úspěšně upraveno.',
        'no-updated'                => 'Oprávnění nelze upravovat.',
        'deleted'                   => 'Oprávnění úspěšně smazáno.',
        'no-deleted'                => 'Oprávnění nelze smazat. Navázáno na routy nebo role.',
        'generated'                 => 'Úspěšně generováno :number oprávnění z routů.',
        'global-enabled'            => 'Vybrané oprávnění byla aktivována.',
        'global-disabled'           => 'Vybrané oprávnění byla deaktivována.',
        'enabled'                   => 'Oprávnění zapnuto.',
        'disabled'                  => 'Oprávnění vypnuto.',
        'no-perm-selected'          => 'Nebylo vybráno žádné oprávnění.',
    ],

    'error'               => [
        'cant-delete-this-permission' => 'Vybrané oprávnění nemůže být odstraněno.',
        'cant-delete-perm-in-use'     => 'Vybrané oprávnění je použito. Nemůže být odstraněno',
        'cant-edit-this-permission'   => 'Vybrané oprávnění nemůže být upraveno.',
    ],

    'page'              => [
        'index'              => [
            'title'             => 'Admin | Oprávnění',
            'description'       => 'list všech oprávnění',
            'table-title'       => 'Oprávnění uživatelů',
        ],
        'show'              => [
            'title'             => 'Admin | Oprávnění | zobrazení',
            'description'       => 'Zobrazení oprávnění: :name',
            'section-title'     => 'Oprávnění detail'
        ],
        'create'            => [
            'title'            => 'Admin | Oprávnění | Vytvořit',
            'description'      => 'Vytvoření nového oprávnění',
            'section-title'    => 'Nové oprávnění'
        ],
        'edit'              => [
            'title'            => 'Admin | Oprávnění | Editace',
            'description'      => 'Editace oprávnění: :name',
            'section-title'    => 'Upravit oprávnění'
        ],
    ],

    'columns'           => [
        'id'                        =>  'ID',
        'name'                      =>  'URL',
        'display_name'              =>  'Název oprávnění',
        'description'               =>  'Popis',
        'created'                   =>  'Vytvořeno',
        'roles'                     =>  'Role',
        'routes'                    =>  'Celkem routů',
        'updated'                   =>  'Upraveno',
        'enabled'                   =>  'Aktivovace/Deaktivace',
        'actions'                   =>  'Akce',
    ],

    'action'               => [
        'create'                => 'Vytvořit nové oprávnění',
        'generate'              => 'Generovat',
        'enabled-select'        => 'Aktivovat vybrané oprávnění',
        'disabled-select'       => 'Deaktivovat vybrané oprávnění',
    ],

];
