<?php return [

    /*
    |--------------------------------------------------------------------------
    | Package Configuration Option
    |--------------------------------------------------------------------------
    */

    'rbacClass' => App\Classes\Authorization\AuthorizationClass::class,

    /**
     * Name of user's class attribute that gives array of roles
     * if you uses many-to-many relationship
     */

    'userRolesAttribute' => 'own_roles',
];
