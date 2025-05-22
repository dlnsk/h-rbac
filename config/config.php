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
     * Allow to throw the exception if permission not found or if policy not pointed well.
     */
    'riseExceptionIfPermissionNotFound' => false,


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
