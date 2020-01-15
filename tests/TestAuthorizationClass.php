<?php

namespace Dlnsk\HierarchicalRBAC\Tests;

use Dlnsk\HierarchicalRBAC\Authorization;


class TestAuthorizationClass extends Authorization
{
	public function getPermissions() {
		return [
			'editPost' => [
					'description' => 'Edit any posts',
					'next' => 'editOwnPost',
				],
			'editOwnPost' => [
					'description' => 'Edit own post',
				],

			//---------------
			'deletePost' => [
					'description' => 'Delete any posts',
				],
		];
	}

	public function getRoles() {
		return [
			'manager' => [
					'editPost',
					'deletePost',
				],
			'user' => [
					'editOwnPost',
				],
		];
	}


	/**
	 * Methods which checking permissions.
	 * Methods should be present only if additional checking needs.
	 */

	public function editOwnPost($user, $post) {
		return $user->id === $post->user_id;
	}

}
