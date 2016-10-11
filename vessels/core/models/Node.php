<?php

namespace core\models;

use Core;
use core\Router;
use core\Request;

class Node {
	private $method;

	
	public function __construct() {
		$method = $this->method = Core::defaultTo(Request::next(), 'main');
		
		$this->init();
		$this->bufferMethod($method);
	}
	
	
	
	
	
	protected function init() {} // No-op. Meant to be overridden.
	
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
	protected function relNodePath() {
		return substr($this->nodePath(), 6);
	}
	
	protected function renderFile($file) {
		$path = Core::join($this->nodePath(), $file) . '.html';
		echo file_get_contents($path);
	}
	
	
	
	
	private function bufferMethod($method) {
		if (!method_exists($this, $method) || !is_callable([$this, $method]) || method_exists('Node', $method)) Response::notFoundPage();
		
		ob_start();
		$config = $this->{$method}();
		$content = ob_get_contents();
		ob_end_clean();
		
		if (is_string($config)) $config = ['app' => $config];
		if (!is_array($config)) $config = [];
		$config += ['js' => [], 'css' => []]; // put default config in there
		
		if (isset($config['app'])) {
			$app = Core::join($this->relNodePath(), $config['app']);
			$config['js'][] = $config['css'][] = $app;
		}
		
		$this->render($content ?: '', $config);
	}
	
	private function render($output, $config) { ?>
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width,initial-scale=1">
			<title><?= ucfirst($this->nodeName()) . ($this->method !== 'main' ? ' | ' . ucfirst($this->method) : '') ?></title>
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
			<?= $this->styles($config['css']) ?>
			<script>window.blizzard = {user: {}}</script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/mithril/0.2.5/mithril.js"></script>
			<script src="/js/lib/mithril-x.js"></script>
		</head>
		<body>
			<?= trim($output) ?>
			<?= $this->scripts($config['js']) ?>
		</body>
		</html>
	<?php }
	
	private function scripts($jsFiles = []) {
		return implode('', array_map(function($module) {
			return '<script src="/js/' . $module . '.js"></script>';
		}, $jsFiles));
	}
	
	private function styles($cssFiles = []) {
		return implode('', array_map(function($file) {
			return '<link rel="stylesheet" href="/css/' . $file . '.css">';
		}, $cssFiles));
	}
}
