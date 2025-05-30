<?php
namespace Dlnsk\HierarchicalRBAC\Backend\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
{
    use HandlesAuthorization;

    public $chains = [
        'managePermissions',
    ];

}
