<?php

namespace core;

use Core;

class Router {
	public static function route() {
		Request::init();
		$firstNode = Core::defaultTo(Request::next(), 'home');
		
		switch ($firstNode) {
			case 'css': return static::serveCss('module-cache');
			case 'js': return static::serveJs('module-cache');
			case 'img': return static::serveImg();
			case 'favicon.ico': return static::serveImg('favicon.png');
		}
		$lastNode = Core::defaultTo(Request::last(), '');
		$extension = Core::extension($lastNode);
		
		switch ($extension) {
			case 'css': return static::serveCss(Core::join('node-cache', $firstNode));
			case 'js': return static::serveJs($firstNode, 'nodes');
		}
		
		return static::serveNode($firstNode);
	}
	
	
	private static function serveCss($folder, $basePath = 'resources') {
		$nextNode = Request::peek();
		if ($nextNode === 'lib') $folder = '';
		$file = Core::join($basePath, $folder, Request::all());
		
		Response::read('text/css', $file);
		return true;
	}
	
	private static function serveJs($folder, $basePath = 'resources') {
		$nextNode = Request::peek();
		if ($nextNode === 'lib') $folder = '';
		$file = Core::join($basePath, $folder, Request::all());
		
		Response::read('text/javascript', $file);
		return true;
	}
	
	private static function serveImg($img = '') {
		if (!$img) $img = Core::join(Request::all());
		$mimeType = Core::extension($img);
		if ($mimeType === 'jpg') $mimeType = 'jpeg';
		
		Response::read('image/' . $mimeType, Core::join('resources', 'img', $img));
		return true;
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
