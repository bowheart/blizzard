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
	
	public static function findCssFile($nodes) {
		if (is_string($nodes)) $nodes = explode('/', $nodes);
		
		$path = Core::join('cache', 'css', implode('/', $nodes));
		if (!file_exists($path)) return null;
		
		return $path;
	}
	
	public static function findJsFile($nodes) {
		if (is_string($nodes)) $nodes = explode('/', $nodes);
		
		$graphType = 'modules';
		if (reset($nodes) === 'nodes') {
			$graphType = 'nodes';
			array_shift($nodes);
		}
		
		$file = array_pop($nodes);
		$pathBase = Core::join($graphType, implode('/' . $graphType . '/', $nodes));
		$pathMid = substr($file, 0, -3);
		if (count($nodes)) $pathMid = Core::join($graphType, $pathMid);
		
		$path = Core::join($pathBase, $pathMid, $file); // look for an implicitly-named module
		
		if (!file_exists($path)) {
			$path = Core::join($pathBase, $file); // look for an explicitly-named module
			
			if (!file_exists($path)) $path = null;
		}
		
		return $path;
	}
	
	
	
	private static function serveCss() {
		$path = static::findCssFile(Request::all());
		if (!$path) Response::notFound();
		
		Response::read('text/css', $path);
	}
	
	private static function serveJs() {
		$path = static::findJsFile(Request::all());
		if (!$path) Response::notFound();
		
		Response::read('text/javascript', $path);
	}
	
	private static function serveImg() {
		$path = Core::join('resources', 'img', implode('/', Request::all()));
		
		Core::assertFile($path);
		Response::read(mime_content_type($path), $path);
	}
	
	public static function serveNode($node) {
		$nodePieces = array_merge([$node], Request::all()); // put the node back on the beginning of the request
		$class = '';
		
		while (count($nodePieces)) {
			$nextPiece = array_pop($nodePieces);
			$class = join('\\', array_filter(array_merge(['nodes'], [implode('\\nodes\\', $nodePieces)], [Core::dashToPascal($nextPiece)])));
			
			if (class_exists($class)) break;
			
			Request::unshift($nextPiece); // if this piece wasn't part of the final class, put it back on the request
		}
		
		// If the node doesn't exist, send them to the not-found node.
		if (!class_exists($class)) {
			$class = '\\nodes\\NotFound';
		}
		
		$instance = new $class($class);
		return $instance;
	}
}
