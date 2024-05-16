<?php return [

    /*
    |--------------------------------------------------------------------------
    | Package Configuration Option
    |--------------------------------------------------------------------------
    */


    /**
     * Name of User model attribute that gives one role or array of roles
     */

    'userRolesAttribute' => 'roles',


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
