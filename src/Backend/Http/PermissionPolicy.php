<?php
namespace Dlnsk\HierarchicalRBAC\Backend\Http;

use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
{
    use HandlesAuthorization;

    public $chains = [
        'managePermissions',
    ];

}
