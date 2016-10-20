<?php

namespace nodes\core;

use Core;
use core\models\Node;
use core\Request;
use core\Response;
use Leafo\ScssPhp\Compiler;

class Cache extends Node {
	private $apps = [];
	private $moduleDirs = [];
	private $moduleMap = [];
	private $toCompile = [];
	private $sassCompiler;
	
	/*
	 * The Process:
	 * 
	 * Spider through the modules folder, finding all js and sass files
	 *   For .js files:
	 *     Map all mithril-x modules to the file they were defined in and their dependencies ($this->moduleMap)
	 *     Copy all files to the resources/js directory (location dependent on dir and filename of file)
	 *   For .scss files:
	 *     Add directories containing files starting with an '_' to the compiler import paths.
	 *     Keep track of all other sass files (add them to the $this->toCompile list)
	 * 
	 * Spider through the nodes folder. Same thing, but don't copy the .js files to resources/js
	 * 
	 * When spidering is done, go through the list of sass files toCompile. For each one:
	 *   Compile and output to resources/css directory (location dependent on dir and filename of file)
	 *   Map all modules defined in the same directory of a sass file to the resulting css file url (in $this->moduleMap)
	 */
	public function main() {
		$render = !Core::is(Request::data('render'), [false, 'false']);
		
		$this->sassCompiler = new Compiler();
		$this->sassCompiler->setFormatter('Leafo\\ScssPhp\\Formatter\\Crunched');
		if ($render) echo '<h3>Generating cache...</h3>';
		
		$this->createModuleMap();
		$this->compileSass();
		
		$moduleMap = ['apps' => $this->apps, 'modules' => $this->moduleMap];
		Core::writeFile(Core::join('resources', 'vessel-cache', 'module-map.json'), json_encode($moduleMap));
		
		if (!$render) Response::ok('Cache Successfully Generated.');
		?>
		<h3>Cache generated successfully.</h3>
		<h4>Apps found: <?= count($this->apps) ?></h4>
		<ul class="styled"><li><?= implode('</li><li>', array_keys($this->apps)) ?></li></ul>
		<h4>Modules found: <?= count($this->moduleMap) ?></h4>
		<ul class="styled"><li><?= implode('</li><li>', array_keys($this->moduleMap)) ?></li></ul>
		<h4>Sass files compiled: <?= count($this->toCompile) ?></h4>
		<ul class="styled"><li><?= implode('</li><li>', array_keys($this->toCompile)) ?></li></ul>
		<?php
	}
	
	/*
		Go through every single .js file and map module names to the file they were defined in and their dependencies.
		Also cache every .js file, such that
			modules/core/core.js -> resources/js/core.js
			modules/core/other.js -> resources/js/core/other.js
			modules/core/other/other.js -> resources/js/core/other.js
	*/
	private function createModuleMap() {
		Core::spider(['modules', 'nodes'], function($filePath, $file, $dir) {
			
			// we only want .js and .scss files
			if (substr($file, -3) === '.js') {
				$this->mapJsFile($filePath, $file, $dir);
			} else if (substr($file, -5) === '.scss') {
				$this->trackSassFile($filePath, $file, $dir);
			}
		});
	}
	
	
	
	// For, e.g., 'modules/core/base/base.js'
	private function cacheJs($filePath, $file, $dir) {
		$dirNodes = $this->normalizePath($file, $dir);
		
		$cacheFilePath = Core::join('resources', $dirNodes, $file);
		Core::writeFile($cacheFilePath, file_get_contents($filePath));
		
		array_shift($dirNodes);
		$urlPath = Core::join('js', $dirNodes, $file); // find the web-facing path (this would go in a script's `src` attribute)-- 'js/core/base.js'
		return $urlPath;
	}
	
	// For, e.g., 'modules/core/base/base.scss'
	private function cacheSass($filePath, $file, $dir) {
		$dirNodes = $this->normalizePath($file, $dir);
		$cssFile = substr($file, 0, strpos($file, '.')) . '.css';
		
		$cacheFilePath = Core::join('resources', $dirNodes, $cssFile);
		$sass = file_get_contents($filePath);
		try {
			$compiled = $this->sassCompiler->compile($sass);
			Core::writeFile($cacheFilePath, $compiled);
		} catch (\Exception $exception) {
			echo '<p>Unable to compile sass file "' . $filePath . '". Message: ' . $exception->getMessage() . '</p>';
		}
		
		array_shift($dirNodes);
		$urlPath = Core::join('css', $dirNodes, $cssFile); // find the web-facing path (this would go in a link's 'href' attribute)-- 'css/core/base.css'
		return $urlPath;
	}
	
	
	
	private function mapJsFile($filePath, $file, $dir) {
		$isNodeFile = substr($filePath, 0, 6) === 'nodes/'; 
		$fileUrl = ($isNodeFile
			? substr($filePath, 6)
			: $this->cacheJs($filePath, $file, $dir));
		
		$contents = file_get_contents($filePath);
		
		$matches = [];
		preg_match_all('/\bm\.define\((.*)function *?\(.*?\)/', $contents, $matches);
		$moduleDefs = $matches[1];
		
		foreach ($moduleDefs as $moduleDef) {
			$deps = $this->unwrapQuotes($moduleDef);
			$name = array_shift($deps);
			if (is_null($name)) return; // No modules detected in this file.
			if (array_key_exists($name, $this->moduleMap)) throw new \Exception('Multiple modules found with name "' . $name . '". File: ' . $filePath);
			
			if ($name === 'app' || substr($name, '-4') === '-app') { // 'app' and 'whatever-app' indicate an app-type module
				$this->apps[$fileUrl] = $deps;
				continue;
			}
			if (!$isNodeFile) $this->addModuleDir($dir, $name);
			$this->moduleMap[$name] = ['file' => $fileUrl, 'deps' => $deps, 'css' => []]; // the css will get set after all spidering is done.
		}
	}
	
	private function trackSassFile($filePath, $file, $dir) {
		if ($file[0] === '_') return $this->sassCompiler->addImportPath($dir);
		
		$this->toCompile[$filePath] = compact('file', 'dir');
	}
	
	
	
	
	private function compileSass() {
		foreach ($this->toCompile as $filePath => $data) {
			$fileUrl = $this->cacheSass($filePath, $data['file'], $data['dir']);
			$this->addCssDir($data['dir'], $fileUrl);
		}
	}
	
	
	
	
	// = = = = = = = =   Helper Methods   = = = = = = = = //
	
	private function addCssDir($dir, $file) {
		if (!isset($this->moduleDirs[$dir])) return; // no modules in this directory
		
		foreach ($this->moduleDirs as $moduleDir => $modules) {
			if (substr($moduleDir, 0, strlen($dir)) !== $dir) continue;
			
			foreach ($modules as $module) {
				$this->moduleMap[$module]['css'][] = $file;
			}
		}
	}
	private function addModuleDir($dir, $name) {
		if (!isset($this->moduleDirs[$dir])) $this->moduleDirs[$dir] = [];
		$this->moduleDirs[$dir][] = $name;
	}
	
	// For, e.g., 'modules/core/base/base.js'
	private function normalizePath($file, $dir) {
		$filename = substr($file, 0, strrpos($file, '.'));
		$dirNodes = explode('/', $dir);
		$firstNode = array_shift($dirNodes);
		array_unshift($dirNodes, substr($firstNode, 0, -1). '-cache');
		if ($firstNode === 'nodes') return $dirNodes; // skip the last bit for files in the 'nodes' folder
		
		$lastNode = end($dirNodes);
		if ($filename === $lastNode) array_pop($dirNodes); // turn 'core/base/base.js' into 'core/base.js'
		
		return $dirNodes;
	}
	
	private function unwrapQuotes($string) {
		$matches = [];
		preg_match_all('/"(.*?)"|\'(.*?)\'/', $string, $matches);
		$captures = $matches[2];
		return $captures;
	}
}
