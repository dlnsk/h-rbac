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
            'editFixedPost',
        ],
        'delete' => [
            'deleteAnyPost',
        ],
    ];


    /**
     * Check user can only edit self created posts.
     */
    public function editOwnPost($user, $post): bool {
        return $user->id === $post->user_id;
    }

    /**
     * Permission model contain post's id which allowed to edit to this user.
     */
    public function editFixedPost($user, $post, $permission): bool {
        return $post->id === $permission->value;
    }
}
