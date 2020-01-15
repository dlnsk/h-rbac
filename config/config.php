<?php return [

    /*
    |--------------------------------------------------------------------------
    | Package Configuration Option
    |--------------------------------------------------------------------------
    */


    /**
     * Name of class which contain list of roles and permissions
     */

    'rbacClass' => App\Classes\Authorization\AuthorizationClass::class,

    /**
     * Name of User model attribute that gives single role of user
     * if you DON'T use many-to-many relationship
     */

    'singleRoleAttribute' => 'role',

    /**
     * Name of User model attribute that gives array of roles
     * if you use many-to-many relationship
     */

    'userRolesAttribute' => 'own_roles',

];
