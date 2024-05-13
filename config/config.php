<?php return [

    /*
    |--------------------------------------------------------------------------
    | Package Configuration Option
    |--------------------------------------------------------------------------
    */


    /**
     * Name of User model attribute that gives single role of user
     * if you DON'T use many-to-many relationship
     */

    'singleRoleAttribute' => 'role',


    /**
     * Name of User model attribute that gives array of roles
     * if you use one-to-many or many-to-many relationships
     */

    'manyRolesAttribute' => 'own_roles',


    /**
     * Name of User model attribute that gives array of extra permissions.
     */
    'permissionsAttribute' => 'permissions',


    /**
     * Built-in application roles and its permissions
     */
    'builtinRoles' => [
        'manager' => [
            'editAnyPost',
            'deleteAnyPost',
            'listAnyReports',
        ],
        'user' => [
            'editOwnPost',
            'listEducationReports',
        ],
    ],

];
