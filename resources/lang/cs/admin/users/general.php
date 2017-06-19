<?php
return [

    'status'              => [
        'created'                   => 'Uživatel byl úspěšně vytvořen',
        'updated'                   => 'Uživatel byl úspěšně upraven',
        'no-updated'                => 'Uživatel nelze upravovat',
        'deleted'                   => 'Uživatel byl úspěšně smazán',
        'no-deleted'                => 'Uživatel nelze smazat.',
        'global-enabled'            => 'Uživatel byl úspěšně aktivován.',
        'global-disabled'           => 'Uživatel byl úspěšně deaktivován.',
        'enabled'                   => 'Uživatel byl úspěšně aktivován.',
        'disabled'                  => 'Uživatel byl úspěšně deaktivován.',
        'no-user-selected'          => 'Žádný uživatel nebyl vybrán.',
        'login-user-ok'             => 'Přihlášení proběhlo úspěšně. Vítejte uživateli ',
    ],

    'error'               => [
        'cant-be-edited'                => 'Uživatel nemůže být upraven.',
        'cant-be-deleted'               => 'Uživatel nemůže být smazán',
        'cant-be-disabled'              => 'Uživatel nemůže být deaktivován.',
        'login-failed-user-disabled'    => 'Účet uživatele byl deaktivován.',
    ],

    'page'              => [
        'index'              => [
            'title'             => 'Admin | Uživatelé',
            'description'       => 'List uživatelů',
            'table-title'       => 'Uživatelé',
        ],
        'create'            => [
            'title'            => 'Admin | User | Vytvořit',
            'description'      => 'vytváření nového uživatele',
            'section-title'    => 'Nový uživatel'
        ],
        'edit'              => [
            'title'            => 'Admin | User | Editování',
            'description'      => 'Editování uživatele: :full_name',
            'section-title'    => 'Editovar uživatele'
        ],
    ],

    'columns'           => [
        'id'                        =>  'ID',
        'username'                  =>  'Přihlašovací jméno',
        'name'                      =>  'Jméno a příjmení',
        'assigned'                  =>  'Assigned',
        'roles'                     =>  'Role celkem',
        'email'                     =>  'Email',
        'password'                  =>  'Heslo',
        'password_confirmation'     =>  'Heslo potvrzení',
        'created'                   =>  'Vytvořeno',
        'updated'                   =>  'Upraveno',
        'actions'                   =>  'Akce',
        'effective'                 =>  'Effective',
        'enabled'                   =>  'Aktivace/Deaktivace',
    ],

    'button'               => [
        'create'    =>  'Vytvořit nového uživatele',
    ],



];

