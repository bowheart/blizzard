<?php

namespace core;

use Core;
use core\Request;
use core\Response;

class Router {
	public static function route() {
		Request::init();
		$route = Core::defaultVal(Request::next(), 'home');
		
		switch ($route) {
			case 'css': return static::serveCss();
			case 'js': return static::serveJs();
			case 'img': return static::serveImg();
			case 'favicon.ico': return static::serveImg('favicon.png');
			default: return static::serveNode($route);
		}
	}
	
	private static function serveCss() {
		$module = Request::next();
		$file = Request::next();
		Response::read('text/css', Core::join('cache', 'css', $module, $file));
	}
	
	private static function serveJs() {
		$module = Request::next();
		$file = Request::next();
		$path = Core::join('modules', $module, 'js', $file);
		
		if (!file_exists($path)) $path = Core::join('blizzard', $path);
		if (!file_exists($path)) $path = Core::join('vessels', $module, 'js', $file);
		if (!file_exists($path)) $path = Core::join('blizzard', $path);
		if (!file_exists($path)) Response::notFound();
		
		Response::read('text/javascript', $path);
	}
	
	private static function serveImg($file = '') {
		if (!$file) $file = Request::next();
		Core::assert($file);
		$path = Core::join('resources', 'img', $file);
		
		Core::assertFile($path);
		Response::read(mime_content_type($path), $path);
	}
	
	public static function serveNode($node) {
		$class = '\\nodes\\' . Core::dashToPascal($node);
		
		// If the node doesn't exist, send them the not-found node.
		if (!class_exists($class)) {
			$class = '\\nodes\\NotFound';
		}
		
		$instance = new $class($class);
		return $instance;
	}
}
