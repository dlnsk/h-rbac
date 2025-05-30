<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Language resource of H-RBAC module
    |--------------------------------------------------------------------------
    */

    'actions' => 'Actions',
    'add' => 'Add',
    'choose' => 'Choose...',
    'conflict' => 'Permission conflict',
    'edit' => 'Edit',
    'exclude' => 'Deny',
    'excluded' => 'Denied',
    'include' => 'Allow',
    'included' => 'Allowed',
    'kind' => 'Kind',
    'param' => 'Parameter',
    'params' => 'Parameters',
    'permission' => 'Permission',
    'permissions_for' => 'Permission for :User',
    'provided_by_role' => 'Provided by one of the roles',
    'role' => 'Role',
    'sure' => 'Are you sure?',
    'user_roles' => 'User roles',
    'value' => 'Value',


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
