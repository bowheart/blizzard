<?php

use core\Response;

class Core {
	public static function assertCallable($thing) {
		if (is_callable($thing)) return true;
		throw new Exception('Parameter for "' . static::caller() . '()" must be callable.');
	}
	
	public static function caller() {
		$info = debug_backtrace()[2];
		return $info['class'] . $info['type'] . $info['function'];
	}
	
	public static function dashToCamel($str) {
		return preg_replace_callback('/-(.)/', function($matches) { return strtoupper($matches[1]); }, $str);
	}
	
	public static function dashToPascal($str) {
		return ucfirst(static::dashToCamel($str));
	}
	
	public static function defaultTo($val1, $val2 = null) {
		return $val1 ?: $val2;
	}
	
	public static function ensureDir($nodes, $dropLastNode = false) {
		if (is_string($nodes)) $nodes = explode('/', $nodes);
		if ($dropLastNode) array_pop($nodes);
		$path = '';
		
		foreach ($nodes as $node) {
			$path = Core::join($path, $node);
			if (!file_exists($path)) mkdir($path);
		}
	}
	
	public static function join() {
		$paths = array();
		foreach (func_get_args() as $arg) {
			if ($arg && is_string($arg)) $paths[] = $arg;
			else if (is_array($arg)) $paths = array_merge($paths, $arg);
		}
		return preg_replace('#/+#', '/', implode('/', $paths));
	}
	
	public static function parseJsonFile($file) {
		if (!$file) return $file;
		$json = json_decode(file_get_contents($file), true);
		if (!$json) return null;
		return $json;
	}
	
	public static function readdir($dir) {
		if (!file_exists($dir)) return [];
		return array_diff(scandir($dir), ['.', '..']);
	}
	
	public static function spider($dir, $modify, $spiderCondition = null) {
		static::assertCallable($modify);
		
		if (!is_callable($spiderCondition)) {
			$spiderCondition = function($filePath, $file, $dir) {
				return true;
			};
		}
		
		$files = static::readdir($dir);
		foreach ($files as $file) {
			$filePath = static::join($dir, $file);
			if (is_dir($filePath) && $spiderCondition($filePath, $file, $dir)) {
				static::spider($filePath, $modify, $spiderCondition);
				continue;
			}
			$modify($filePath, $file, $dir);
		}
	}
	
	public static function writeFile($file, $contents) {
		$nodes = explode('/', $file);
		static::ensureDir($nodes, true);
		
		file_put_contents($file, $contents);
	}
}
