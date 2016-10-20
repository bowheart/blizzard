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
		static::$data = $_GET + $_POST + Core::defaultTo(json_decode(file_get_contents('php://input'), true), []);
		
		$uri = static::data('uri');
		if (is_null($uri)) {
			$uri = (isset($_SERVER['REQUEST_URI']) ? substr($_SERVER['REQUEST_URI'], 1) : '');
		}
		if (strpos($uri, '?')) $uri = substr($uri, 0, strpos($uri, '?'));
		
		static::$uri = $uri;
		static::$uriNodes = array_filter(explode('/', $uri));
		static::$nodes = array_filter(explode('/', $uri));
		
		$method = static::data('method');
		if (is_null($method)) {
			$method = (isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '');
		}
		static::$method = $method;
		
	}
	
	public static function all() {
		$all = static::$nodes;
		static::$nodes = [];
		return $all;
	}
	
	public static function data($key = null) {
		if ($key) {
			return array_key_exists($key, static::$data) ? static::$data[$key] : null;
		}
		return static::$data;
	}
	
	public static function last() {
		return end(static::$nodes);
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
	
	public static function peek() {
		return reset(static::$nodes);
	}
	
	public static function push($node) {
		return static::$nodes[] = $node;
	}
	
	public static function unshift($node) {
		return array_unshift(static::$nodes, $node);
	}
	
	public static function uri() {
		return static::$uri;
	}
}
