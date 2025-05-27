<?php
namespace Dlnsk\HierarchicalRBAC\Backend\Traits;

use Dlnsk\HierarchicalRBAC\Backend\Models\Permission;

trait WithPermissions {

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }

}
