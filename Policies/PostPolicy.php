<?php
namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    public $chains = [
        'edit' => [
            'editAnyPost',
            'editPostInCategory',
            'editOwnPost',
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
     * The permissions variable contains a collection of permission overrides which field 'value' keeps
     * category IDs whose posts are allowed to be edited by the given user.
     * If there aren't any records, it contains null.
     */
    public function editPostInCategory($authorizedUser, $post, $permissions): bool {
        return $permissions && $permissions->contains('value', $post->category_id);
    }

    /**
     * Backend UI
     *
     * Tells to backend to add UI element like select or input with appropriate value(s) for this permission.
     * Return array to show <select> element or type of <input> (as you see here) to allow direct input value.
     * This kind of method should have postfix 'Params'.
     *
     * @return string|array
     */
    public function editPostInCategoryParams() {
        return '##_number_##';
    }
}
