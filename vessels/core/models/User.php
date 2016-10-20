<?php

namespace core\models;

use core\Session;

class User {
	private static $user;
	
	public static function is($role) {
		$user = static::current();
		return $user->role() === $role;
	}
	
	
	private static function current($user = null) {
		if ($user) {
			if (!($user instanceof UserModel)) $user = new UserModel($user);
			static::$user = $user;
			return $user;
		}
		if (!static::$user) $user = []; // if one hasn't been set, create an empty one (for guests)
		
		if (is_array($user)) {
			// Set the current user to the given user.
			Session::write('user', $user);
			static::$user = new UserModel($user);
		}
		return static::$user;
	}
}




/**
 * This guy should never be used outside of its controller (above)
*/
class UserModel extends ModelBase {
	public function __construct($data) {
		if (array_key_exists('id', $data)) $this->data['id'] = $data['id'];
		if (array_key_exists('username', $data)) $this->data['username'] = $data['username'];
		
		$this->data['role'] = (array_key_exists('role', $data) ? $data['role'] : 'guest');
	}
}
