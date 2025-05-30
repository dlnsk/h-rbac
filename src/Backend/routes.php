<?php

use Dlnsk\HierarchicalRBAC\Backend\Http\PermissionController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => config('h-rbac.permissionsUI.routePrefix'),
    'middleware' => config('h-rbac.permissionsUI.routeMiddlewares')
], function () {
    Route::resource('{user}/permissions', PermissionController::class)->only(['index', 'edit', 'store', 'destroy']);
});
