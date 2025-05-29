<?php
namespace Dlnsk\HierarchicalRBAC\Traits;

use Dlnsk\HierarchicalRBAC\Models\Permission;

trait WithPermissions {

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }

}
