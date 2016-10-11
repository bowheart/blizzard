<?php

namespace core;

use Core;
use core\Request;

class Router {
	public static function route() {
		Request::init();
		$route = Core::defaultTo(Request::next(), 'home');
		
		switch ($route) {
			case 'css': return static::serveCss();
			case 'js': return static::serveJs();
			case 'img': return static::serveImg();
			case 'favicon.ico': return static::serveImg('favicon.png');
			default: return static::serveNode($route);
		}
	}
	
	
	private static function serveCss() {
		
	}
	
	private static function serveJs() {
		
	}
	
	private static function serveImg() {
		
	}
	
	public static function serveNode($node, $namespace = '') {
		if ($namespace) $namespace .= '\\';
		$class = 'nodes\\' . $namespace . Core::dashToPascal($node);
		
		if (!class_exists($class)) {
			$next = Request::next();
			return $next
				? static::serveNode($next, $namespace . $node)
				: static::serveNode('not-found');
		}
		
		return new $class();
	}
}
