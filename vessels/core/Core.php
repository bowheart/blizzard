<?php

class Core {
	public static function assert($thing, $verbose = false) {
		if (!empty($thing)) return true;
		Response::error($verbose ? 'Parameter for ' . static::getCallingFunction() . ' must be set' : null);
	}
	public static function assertArray($thing, $verbose = false) {
		if (is_array($thing)) return true;
		Response::error($verbose ? 'Parameter for ' . static::getCallingFunction() . ' must be an array' : null);
	}
	public static function assertFile($file) {
		if (file_exists($file)) return true;
		Response::notFound();
	}
	public static function assertString($thing, $verbose = false) {
		if (is_string($thing)) return true;
		Response::error($verbose ? 'Parameter for ' . static::getCallingFunction() . ' must be a string' : null);
	}
	
	
	public static function dashToCamel($str) {
		return preg_replace_callback('/-(.)/', function($matches) { return strtoupper($matches[1]); }, $str);
	}
	public static function dashToPascal($str) {
		return ucfirst(static::dashToCamel($str));
	}
	
	
	public static function defaultVal($val1, $val2) {
		return $val1 ? $val1 : $val2;
	}
	
	
	public static function ensureDir($dir) {
		if (file_exists($dir)) return true;
		
		mkdir($dir);
		return true;
	}
	
	
	public static function getCallingFunction() {
		return debug_backtrace()[2]['function'];
	}
	
	
	public static function join() {
		$paths = array();
		
		foreach (func_get_args() as $arg) {
			if ($arg !== '' and is_string($arg)) $paths[] = $arg;
		}
		return preg_replace('#/+#','/', implode('/', $paths));
	}
	
	
	public static function page($name, $isCorePage) {
		$path = static::join($isCorePage ? 'blizzard' : '', 'pages', $name, 'page.php');
		static::assertFile($path);
		
		require $path;
	}
	
	
	public static function parseJsonFile($file) {
		if (!$file) return $file;
		$json = json_decode(file_get_contents($file), true);
		if (!$json) return null;
		return $json;
	}
	
	
	public static function parseModel($data) {
		if (is_object($data) && method_exists($data, 'toJSON')) return $data->toJSON();
		return $data;
	}
	
	
	public static function plugin($name) {
		$path = static::join('vendor', $name . '.php');
		static::assertFile($path);
		
		require_once $path;
	}
	
	
	public static function readdir($dir) {
		if (!file_exists($dir)) return [];
		return array_diff(scandir($dir), ['.', '..']);
	}
	
	
	public static function spiderLoad($dir) {
		$contents = static::readdir($dir);
		foreach ($contents as $file) {
			$path = $dir . DIRECTORY_SEPARATOR . $file;
			if (is_dir($path)) {
				static::spiderLoad($path);
				continue;
			}
			require_once $path;
		}
	}
	
	
	public static function toArray($thing) {
		if (!$thing || is_array($thing)) return $thing;
		if (is_string($thing)) return [$thing];
		if (is_object($thing)) return get_object_vars($thing);
	}
}
