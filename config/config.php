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
     * Setup of Permissions Backend UI
     */
    'permissionsUI' => [

        /**
         * Enable or disable backend UI
         */
        'enabled' => false,

        /**
         * Defines prefix for UI routes. Structure of route is
         * `/{prefix}/{user}/permissions/{params}`,
         */
        'routePrefix' => '',

        /**
         * Array of middlewares that filter the request to backend UI.
         */
        'routeMiddlewares' => ['auth'],

        /**
         * Defines a layout for backend's views. We use `header` and `content` sections
         */
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
