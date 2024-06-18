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
    public function editOwnPost($authorizedUser, $post): bool {
        return $authorizedUser->id === $post->user_id;
    }

    /**
     * Permission model contain post's id which allowed to edit to this user.
     */
    public function editFixedPost($authorizedUser, $post, $permission): bool {
        return $post->id === $permission->value;
    }
}
