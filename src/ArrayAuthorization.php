<?php

namespace Dlnsk\HierarchicalRBAC;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;


class ArrayAuthorization
{
	public function getPermissions() {
		return [];
	}

	public function getRoles() {
		return [];
	}

	private function testUsingUserMethod($user, $initial_ability, $current_ability, $arguments) {
		$methods = get_class_methods($this);
		$method = Str::camel($current_ability);
		if (in_array($method, $methods)) {
			// Преобразуем массив в единичный элемент если он содержит один элемент
			// или это ассоциативный массив с любым кол-вом элементов
			if (!empty($arguments)) {
				$arg = (count($arguments) > 1 or array_keys($arguments)[0] !== 0) ? $arguments : last($arguments);
			} else {
				$arg = null;
			}
			return $this->$method($user, $arg, $initial_ability) ? true : false;
		}
		return true;
	}

	/**
	 * Checking permission for choosed user
	 *
	 * @return boolean
	 */
	public function checkAbility($user, $user_abilities, $ability, $arguments)
	{
		// Ищем разрешение для данной роли среди наследников текущего разрешения
		$permissions = $this->getPermissions();
		$current = $ability;
		// Если для разрешения указана замена - элемент 'equal', то проверяется замена
		// (только при наличии оригинального разрешения в роли).
		// Callback оригинального не вызывается.
		if (in_array($current, $user_abilities) and isset($permissions[$current]['equal'])) {
			$current = $permissions[$current]['equal'];
		}

		$i = 0;
		$suitable = false;
		while (true) {
			if ($i++ > 100) {
				throw new \Exception("Seems like permission '{$ability}' is in infinite loop");
			}

			if (in_array($current, $user_abilities)) {
				$suitable = $suitable || $this->testUsingUserMethod($user, $ability, $current, $arguments);
			}
			if (isset($permissions[$current]['next']) and !$suitable) {
				$current = $permissions[$current]['next'];
			} else {
				return $suitable ? true : null;
			}
		}
		return null;
	}


	public function checkPermission($user, $ability, $arguments)
	{
		$attribute = config('h-rbac.userRolesAttribute');
		$user_roles = Arr::wrap($user->role ?? null) ?: Arr::wrap($user->$attribute ?? null);

		if (in_array('admin', $user_roles)) {
			return true;
		}

		// У пользователя роли, которых нет в списке ролей приложения
		$application_roles = array_keys($this->getRoles());
		$both_roles = array_intersect($application_roles, $user_roles);
		if (!count($both_roles)) {
			return null;
		}

		$abilities = $this->getRoles();
		$user_abilities = [];
		foreach ($both_roles as $role_name) {
			$user_abilities = array_merge($user_abilities, $abilities[$role_name]);
		}
		$result = $this->checkAbility($user, $user_abilities, $ability, $arguments);

		return is_bool($result) ? $result : null;
	}



	/**
	 * Return model of given class or exception if can't
	 *
	 * @param  class 			$class 		This is a class which instance we need.
	 * @param  Model|integer 	$id 		Instance or its ID
	 *
	 * @return Model|exception
	 */
	public function getModel($class, $id)
	{
		if ($id instanceof $class) {
			return $id;
		} elseif (ctype_digit(strval($id))) { // целое число в виде числа или текстовой строки
			return $class::findOrFail($id);
		} else {
			//TODO: Использовать свое исключение
			throw new \Exception("Can't get model.", 1);
		}
	}

}
