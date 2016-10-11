<?php

namespace nodes\core;

use Core;
use core\models\Node;
use Leafo\ScssPhp\Compiler;

class Cache extends Node {
	private $appFiles = [];
	private $moduleMap = [];
	
	public function main() {
		echo '<h4>Generating cache...</h4>';
		
		$this->createModuleMap();
		//$this->spider('nodes');
		
		var_dump($this->appFiles);
		
//		echo '<h4>Cache generated successfully. Apps found: ' . count($this->appFiles) . '</h4><ul><li>' . implode('</li><li>', $this->appFiles) . '</li></ul>';
	}
	
	/*
		Go through every single .js file and map module names to the file they were defined in and their dependencies.
		Also cache every .js file, such that
			modules/core/core.js -> resources/js/core.js
			modules/core/other.js -> resources/js/core/other.js
			modules/core/other/other.js -> resources/js/core/other.js
	*/
	private function createModuleMap() {
		Core::spider('modules', function($filePath, $file, $dir) {
			// we only want .js and .scss files
			if (substr($file, -3) === '.js') {
				$this->mapJsFile($filePath, $file, $dir);
			} else if (substr($file, -5) === '.scss') {
				$this->mapSassFile($filePath, $file, $dir);
			}
		});
	}
	private function unwrapQuotes($string) {
		$matches = [];
		preg_match_all('/"(.*?)"|\'(.*?)\'/', $string, $matches);
		$captures = $matches[2];
		return $captures;
	}
	
	
	private function spider($path) {
		$files = Core::readdir($path);
		foreach ($files as $file) {
			$filePath = Core::join($path, $file);
			
			if (is_dir($filePath)) {
				$this->spider($filePath);
				continue;
			}
			
			if (strpos($file, 'app.js') !== false) $this->cacheApp($filePath);
		}
	}
	
	private function cacheApp($filePath) {
		$this->appFiles[] = $filePath;
		
		ob_start();
		$jsFiles = [];
		$cssFiles = ['nodes/core/_config.scss', 'nodes/core/core.scss', 'nodes/core/custom.scss'];
		$this->moduleDeps($filePath, $jsFiles, $cssFiles);
		$content = ob_get_contents();
		ob_end_clean();
		
		// Write the js to a file.
		Core::writeFile(Core::join('resources', 'js', substr($filePath, 6)), $content);
		
		// Compile the sass and write the resulting css to a file.
		$filePath = substr($filePath, 0, -3) . '.css';
		$sass = '';
		foreach ($cssFiles as $cssFile) {
			$sass .= file_get_contents($cssFile);
		}
		$compiler = new Compiler();
		$compiler->setFormatter('Leafo\\ScssPhp\\Formatter\\Crunched');
		$css = $compiler->compile($sass);
		Core::writeFile(Core::join('resources', 'css', substr($filePath, 6)), $css);
	}
	
	// For, e.g., 'modules/core/base/base.js'
	private function cacheJs($filePath, $file, $dir) {
		$filename = substr($file, 0, strrpos($file, '.'));
		$dirNodes = explode('/', $dir);
		array_shift($dirNodes); // get rid of the 'modules' node
		$lastNode = end($dirNodes);
		if ($filename === $lastNode) array_pop($dirNodes); // turn 'core/base/base.js' into 'core/base.js'
		
		$urlPath = Core::join('js', $dirNodes, $file); // find the web-facing path (this would go in a script's `src` attribute)-- 'js/core/base.js'
		
		$cacheFilePath = Core::join('resources', 'js', $dirNodes, $file);
		Core::writeFile($cacheFilePath, file_get_contents($filePath));
		
		return $urlPath;
	}
	
	private function cacheSass($filePath) {
		
	}
	
	private function mapJsFile($filePath, $file, $dir) {
		$fileUrl = $this->cacheJs($filePath, $file, $dir);
		$contents = file_get_contents($filePath);
		
		$matches = [];
		preg_match_all('/\bm\.define\((.*)function *?\(.*?\)/', $contents, $matches);
		$moduleDefs = $matches[1];
		
		foreach ($moduleDefs as $moduleDef) {
			$deps = $this->unwrapQuotes($moduleDef);
			$name = array_shift($deps);
			if (is_null($name)) return; // No modules detected in this file.
			if (array_key_exists($name, $this->appFiles)) throw new \Exception('Multiple modules found with name "' . $name . '". File: ' . $filePath);
			
			$this->appFiles[$name] = ['file' => $fileUrl, 'deps' => $deps];
		}
	}
	
	private function mapSassFile($filePath, $file, $dir) {
		
	}
	
	// Recursively find the dependency tree of the given mithril-x module (should start with an app-type module).
	private function moduleTree($module, &$jsFiles, &$cssFiles) {
		$file = $this->findJsFile($module);
		if (!$file) return;
		
		$this->loadAssets(dirname($file), $cssFiles);
		
		$jsFiles[] = $module;
		$this->moduleDeps($file, $jsFiles, $cssFiles);
	}
	
	/**
	 * For, e.g. $nodes="core/base.js"
	 */
	private static function findJsFile($nodes) {
		if (is_string($nodes)) $nodes = explode('/', $nodes);
		
		// Look for 'modules/core/base.js'
		$path = Core::join('modules', implode('/', $nodes));
		
		// Look for 'modules/core/base/base.js'
		if (!file_exists($path)) {
			$file = array_pop($nodes);
			$splitFile = explode('.', $file);
			$nodes[] = array_shift($splitFile);
			$path = Core::join('modules', implode('/', $nodes), $file);
			
			if (!file_exists($path)) $path = null;
		}
		return $path;
	}
	
	// Load all the assets for the module at ($modulePath)
	private function loadAssets($modulePath, &$cssFiles) {
		$files = array_merge(Core::readdir($modulePath), array_map(function($file) {
			return Core::join('_assets', $file);
		}, Core::readdir(Core::join($modulePath, '_assets'))));
		
		foreach ($files as $file) {
			$filePath = Core::join($modulePath, $file);
			
			if (is_dir($filePath)) continue; // ignore subfolders (submodules and whatnot)
			
			// It's a file. Look for .js and .scss files
			$splitFile = explode('.', $file);
			$ext = end($splitFile);
			if ($ext !== 'js' && $ext !== 'scss') continue;
			
			// It's a .js or .scss file. Load it.
			if ($ext === 'scss') {
				if (substr($file, 0, 1) === '_') { // config sass files get priority (files starting with '_')
					array_unshift($cssFiles, $filePath);
					continue;
				}
				
				$cssFiles[] = $filePath;
			}
		}
	}
	
	private function moduleDeps($file, &$jsFiles, &$cssFiles) {
		$contents = file_get_contents($file);
		
		echo $contents; // so it gets outputted by the buffer
		
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
				if (in_array($nextModule, $jsFiles)) continue;
				$this->moduleTree($nextModule, $jsFiles, $cssFiles);
			}
		}
	}
}
