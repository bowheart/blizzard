<?php

namespace core;

session_start();

/**
 * Basic crud operations.
*/
class Session {
	public static function read($key) {
		if (!array_key_exists($key, $_SESSION)) return null;
		return $_SESSION[$key];
	}
	
	public static function write($key, $val) {
		$_SESSION[$key] = $val;
		return $val;
	}
	
	public static function update($key, $val) {
		return static::write($key, $val);
	}
	
	public static function delete($key) {
		unset($_SESSION[$key]);
	}
}
