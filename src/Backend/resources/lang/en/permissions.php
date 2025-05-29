<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Language resource of H-RBAC module
    |--------------------------------------------------------------------------
    */

    'permission' => 'Permission',
    'permissions_for' => 'Permission for :User',
    'role' => 'Role',
    'user_roles' => 'User roles',
    'kind' => 'Kind',
    'param' => 'Parameter',
    'params' => 'Parameters',
    'actions' => 'Actions',
    'provided_by_role' => 'Provided by one of the roles',
    'conflict' => 'Permission conflict',
    'edit' => 'Edit',
    'sure' => 'Are you sure?',
    'choose' => 'Choose...',
    'value' => 'Value',
    'add' => 'Add',

    'include' => 'Allow',
    'included' => 'Allowed',
    'exclude' => 'Deny',
    'excluded' => 'Denied',


    'PermissionPolicy' => [
        '_description'   => 'Working with permissions',

        'managePermissions'   => 'Manage user\'s permissions',
    ],


    'PostPolicy' => [
        '_description'   => 'Manage posts',

        'edit' => 'Edit posts',
        'editAnyPost' => 'Edit any posts',
        'editOwnPost' => 'Edit own posts',
        'editFixedPost' => 'Edit allowed posts',

        'delete' => 'Delete posts',
        'deleteAnyPost' => 'Delete any posts',
    ],
];
