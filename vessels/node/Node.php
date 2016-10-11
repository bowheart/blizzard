<?php

use core\Request;
use core\Response;
use core\Router;

class Node {
	private $method;

	
	public function __construct() {
		$method = $this->method = Core::defaultVal(Request::next(), 'main');
		
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
	
	protected function renderFile($file) {
		$path = Core::join($this->nodePath(), $file) . '.html';
		echo file_get_contents($path);
	}
	
	
	
	
	
	private function bufferMethod($method) {
		if (!method_exists($this, $method) || !is_callable([$this, $method]) || method_exists('Node', $method)) Response::notFoundPage();
		
		ob_start();
		$appFile = $this->{$method}();
		$content = ob_get_contents();
		ob_end_clean();
		
		if (!$appFile) $appFile = $method;
		$appModule = Core::join($this->nodePath(), $appFile . '.js');
		
		$modules = [];
		$cssFiles = ['core/core.css', 'core/base/base.css'];
		if ($appModule) $this->moduleTree($appModule, $modules, $cssFiles);
		
		$this->render($content, $modules, $cssFiles);
	}
	
	private function moduleSpider($originalFile, $dir, &$tree, &$cssFiles) {
		$files = Core::readdir($dir);
		
		// Look for .js and .scss files
		foreach ($files as $file) {
			$filePath = Core::join($dir, $file);
			if ($filePath === $originalFile) continue; // don't duplicate the original file
			
			if (is_dir($filePath)) {
				if ($file === 'modules') continue; // don't load sub-modules
				
				$this->moduleSpider($file, $dir, $tree, $cssFiles);
				continue;
			}
			
			// It's a file. Look for .js and .scss files
			$splitFile = explode('.', $file);
			$ext = end($splitFile);
			if ($ext !== 'js' && $ext !== 'scss') continue;
			
			// It's a .js or .scss file. Load it.
			if ($ext === 'scss') {
				array_pop($splitFile);
				$cssFile = implode('.', $splitFile);
				if (substr($cssFile, 0, 1) === '_') continue; // ignore config sass files (starting with '_')
				
				$cssFilePath = Core::join(preg_replace('/modules\//', '', $dir), $cssFile . '.css');
				
				$cssFiles[] = $cssFilePath;
			} else if ($ext === 'js') {
				$jsFilePath = preg_replace('/modules\//', '', $filePath);
				
				$tree[] = $jsFilePath;
			}
		}
	}
	
	private function moduleTree($module, &$tree, &$cssFiles) {
		$file = Router::findJsFile($module);
		if (!$file) return [];
		
		$this->moduleSpider($file, dirname($file), $tree, $cssFiles);
		
		$tree[] = $module;
		$contents = file_get_contents($file);
		
		$depLists = [];
		preg_match_all('/m.define\(.*?, *(.*),.*?function/', $contents, $depLists);
		$depLists = $depLists[1];
		
		foreach ($depLists as $list) {
			$unwrapped = []; // unwrap from the array ("[]")
			preg_match('/\[(.*)\]/', $list, $unwrapped);
			$unwrapped = (array_key_exists(1, $unwrapped) ? $unwrapped[1] : $list);
			
			$list = explode(',', $unwrapped);
			
			foreach ($list as $item) {
				$unwrapped = []; // unwrap from quotes
				preg_match('/("|\')(.*)("|\')/', $item, $unwrapped);
				$unwrapped = $unwrapped[2];
				
				$nextModule = preg_replace('/\./', '/', $unwrapped) . '.js';
				if (in_array($nextModule, $tree)) continue;
				$this->moduleTree($nextModule, $tree, $cssFiles);
			}
		}
		
		return $tree;
	}
	
	private function render($output = '', $modules = [], $cssFiles = []) { ?>
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width,initial-scale=1">
			<title><?= ucfirst($this->nodeName()) . ($this->method !== 'main' ? ' | ' . ucfirst($this->method) : '') ?></title>
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
			<?= $this->styles($cssFiles) ?>
			<script>window.blizzard = {user: {}}</script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/mithril/0.2.5/mithril.js"></script>
			<script src="/js/lib/mithril-x.js"></script>
		</head>
		<body>
			<?= trim($output) ?>
			<?= $this->scripts($modules) ?>
		</body>
		</html>
	<?php }
	
	private function scripts($modules = []) {
		return implode('', array_map(function($module) {
			return '<script src="/js/' . $module . '"></script>';
		}, $modules));
	}
	
	private function styles($cssFiles = []) {
		return implode('', array_map(function($file) {
			return '<link rel="stylesheet" href="/css/modules/' . $file . '">';
		}, $cssFiles));
	}
}
