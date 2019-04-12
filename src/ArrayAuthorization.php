<?php

namespace Dlnsk\HierarchicalRBAC;


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
		$method = camel_case($current_ability);
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
	public function checkPermission($user, $ability, $arguments)
	{
		if ($user->role === 'admin') {
			return true;
		}

		// У пользователя роль, которой нет в списке
		$roles = $this->getRoles();
		if (!isset($roles[$user->role])) {
			return null;
		}

		// Ищем разрешение для данной роли среди наследников текущего разрешения
		$role = $roles[$user->role];
		$permissions = $this->getPermissions();
		$current = $ability;
		// Если для разрешения указана замена - элемент 'equal', то проверяется замена
		// (только при наличии оригинального разрешения в роли).
		// Callback оригинального не вызывается.
		if (in_array($current, $role) and isset($permissions[$current]['equal'])) {
			$current = $permissions[$current]['equal'];
		}

		$i = 0;
		$suitable = false;
		while (true) {
			if ($i++ > 100) {
				throw new \Exception("Seems like permission '{$ability}' is in infinite loop");
			}

			if (in_array($current, $role)) {
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
