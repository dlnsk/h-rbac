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


    'permissionsUI' => [
        'enabled' => false,
        'routePrefix' => '',
        'routeMiddlewares' => ['auth'],
        'baseLayout' => 'layout.app',
    ],


    /**
     * Built-in application roles and its permissions
     */
    'builtinRoles' => [
        'manager' => [
            'editAnyPost',
            'deleteAnyPost',
            'listAnyReports',

            'managePermissions',
        ],
        'user' => [
            'editOwnPost',
            'listEducationReports',
        ],
    ],

];
