<?php
namespace App\Policies;

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
     * Permissions variable contains collection with post ids which allowed to edit to this user.
     * If there aren't any records, it contains null.
     */
    public function editFixedPost($authorizedUser, $post, $permissions): bool {
        return $permissions && $permissions->contains('value', $post->id);
    }

    /**
     * Tells to backend to add UI element like select or input with appropriate value(s) for this permission.
     * Feel free to return any value that you want. You can also return type of input (as you see here).
     * This kind of method should have postfix 'Params'.
     *
     * @return string
     */
    public function editFixedPostParams() {
        return '##_number_##';
    }
}
