<?php
namespace Dlnsk\HierarchicalRBAC\Tests\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class ReportPolicy
{
    use HandlesAuthorization;

    public $chains = [
        'list' => [
            'listAnyReports',
            'listEducationReports',
        ],
    ];


    public function listEducationReports($authorizedUser, $args): bool {
        return $args['kind'] === 'edu';
    }
}
