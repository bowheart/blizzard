<?php

namespace core;

use Core;

class Request {
	private static $uri;
	private static $uriNodes; // permanent
	private static $nodes; // temporary -- pieces get shifted off this one
	private static $method;
	private static $data;
	
	public static function init() {
		static::$uri = substr($_SERVER['REQUEST_URI'], 1);
		if (strpos(static::$uri, '?')) static::$uri = substr(0, strpos(static::$uri, '?'));
		
		$uri = static::$uri;
		static::$uriNodes = array_filter(explode('/', $uri));
		static::$nodes = array_filter(explode('/', $uri));
		static::$method = $_SERVER['REQUEST_METHOD'];
		
		static::$data = $_GET + $_POST + Core::defaultVal(Core::toArray(json_decode(file_get_contents('php://input'))), []);
	}
	
	public static function all() {
		return static::$nodes;
	}
	
	public static function data($key = null) {
		if ($key) {
			return array_key_exists($key, static::$data) ? static::$data[$key] : null;
		}
		return static::$data;
	}
	
	public static function method() {
		return static::$method;
	}
	
	public static function next() {
		return array_shift(static::$nodes);
	}
	
	public static function nodes() {
		return static::$uriNodes;
	}
	
	public static function uri() {
		return static::$uri;
	}
	
	
	public static function isGet() {
		return static::$method === 'GET';
	}
	public static function isPost() {
		return static::$method === 'POST';
	}
}
