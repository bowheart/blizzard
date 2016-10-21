<?php

namespace core\models;

use Core;
use core\Router;
use core\Request;
use core\Response;

class Node {
	private $method;
	private $moduleMap = [];
	private $cssFiles = [];
	private $jsFiles = [];

	
	public function __construct() {
		$method = $this->method = Core::defaultTo(Request::next(), 'main');
		
		$this->init();
		$this->bufferMethod($method);
	}
	
	
	
	
	
	protected function init() {} // No-op. Meant to be overridden.
	
	protected function app($file) {
		$file = ($file[0] === '/' ? substr($file, 1) : Core::join($this->webPath(), $file));
		if (Core::extension($file) !== 'js') $file .= '.js';
		
		$moduleMap = Core::parseJsonFile(Core::join('resources', 'vessel-cache', 'module-map.json'));
		$appDeps = $moduleMap['apps'][$file];
		if (!in_array($file, $this->jsFiles)) $this->jsFiles[] = $file;
		
		$this->moduleMap = $moduleMap;
		$this->loadModule($appDeps);
	}
	
	protected function css() {
		$files = func_get_args();
		foreach ($files as $file) {
			if (strpos($file, '//') === false) { // if it isn't an absolute path, add '.css' to it (if it doesn't have it)
				$file = ($file[0] === '/' ? substr($file, 1) : Core::join($this->webPath(), $file));
				if (substr($file, -4) !== '.css') $file .= '.css';
			}
			if (!in_array($file, $this->cssFiles)) $this->cssFiles[] = $file;
		}
	}
	
	protected function js() {
		$files = func_get_args();
		foreach ($files as $file) {
			if (strpos($file, '//') === false) { // if it isn't an absolute path, add '.js' to it (if it doesn't have it)
				$file = ($file[0] === '/' ? substr($file, 1) : Core::join($this->webPath(), $file));
				if (substr($file, -3) !== '.js') $file .= '.js';
			}
			if (!in_array($file, $this->jsFiles)) $this->jsFiles[] = $file;
		}
	}
	
	// e.g. nodes/not-found/NotFound.php -> nodes\NotFound
	protected function nodeClass() {
		return get_class($this);
	}
	
	// e.g. nodes/not-found/NotFound.php -> NotFound
	protected function nodeName() {
		$classNodes = explode('\\', $this->nodeClass());
		return array_pop($classNodes);
	}
	
	// e.g. nodes/not-found/NotFound.php -> nodes/not-found
	protected function nodePath() {
		$reflector = new \ReflectionClass(get_called_class());
		$absPath = dirname($reflector->getFileName());
		return substr($absPath, strpos($absPath, '/nodes/') + 1);
	}
	
	// e.g. nodes/not-found/NotFound.php -> not-found
	protected function webPath() {
		return substr($this->nodePath(), 6);
	}
	
	protected function renderFile($file) {
		$path = Core::join($this->nodePath(), $file) . '.html';
		echo file_get_contents($path);
	}
	
	
	
	
	/*
	 * Find and buffer the output of the given method on this node.
	 * The method can return a config object containing bootstrap data, and/or an app file
	 */
	private function bufferMethod($method) {
		if (!method_exists($this, $method) || !is_callable([$this, $method]) || method_exists('Node', $method)) Response::notFoundPage();
		
		ob_start();
		$config = $this->{$method}();
		$content = ob_get_contents();
		ob_end_clean();
		
		if (is_string($config)) $config = ['app' => $config];
		if (!is_array($config)) $config = [];
		
		if (isset($config['app'])) {
			$this->css($config['app']);
			$this->js($config['app']);
			$this->app($config['app']);
		}
		
		$this->render($content ?: '', $config);
	}
	
	private function loadModule($deps) {
		foreach ($deps as $dep) {
			$module = $this->moduleMap['modules'][$dep];
			if (!$module) continue; // module not found
			
			$this->loadModule($module['deps']);
			$file = $module['file'];
			if (!in_array($file, $this->jsFiles)) $this->jsFiles[] = $file;
		}
	}
	
	private function render($output, $config) { ?>
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width,initial-scale=1">
			<title><?= ucfirst($this->nodeName()) . ($this->method !== 'main' ? ' | ' . ucfirst($this->method) : '') ?></title>
			<link rel="stylesheet" href="/core/core.css">
			<link rel="stylesheet" href="/core/custom.css">
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
			<?= $this->styles() ?>
			<script>window.blizzard = {user: {}}</script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/mithril/0.2.5/mithril.js"></script>
			<script src="/js/lib/mithril-x.js"></script>
		</head>
		<body>
			<?= trim($output) ?>
			<?= $this->scripts() ?>
		</body>
		</html>
	<?php }
	
	private function scripts() {
		return implode('', array_map(function($module) {
			return '<script src="/' . $module . '"></script>';
		}, $this->jsFiles));
	}
	
	private function styles($cssFiles = []) {
		return implode('', array_map(function($file) {
			return '<link rel="stylesheet" href="/' . $file . '">';
		}, $this->cssFiles));
	}
}
