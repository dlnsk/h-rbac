<?php
namespace Dlnsk\HierarchicalRBAC\Backend\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    const INCLUDE = 'include';
    const EXCLUDE = 'exclude';

    public $timestamps = false;
    protected $fillable = ['name', 'value', 'action'];
}
