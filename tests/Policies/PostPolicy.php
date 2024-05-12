<?php
namespace Dlnsk\HierarchicalRBAC\Tests\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    public $chains = [
        'edit' => [
            'editAnyPost',
            'editOwnPost',
        ],
        'delete' => [
            'deleteAnyPost',
        ],
    ];


    public function editOwnPost($user, $post): bool {
        return $user->id === $post->user_id;
    }
}
