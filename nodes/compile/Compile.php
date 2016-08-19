<?php

namespace nodes;

use Core;
use Node;

Core::plugin(Core::join('leafo', 'scssphp', 'scss.inc'));
use Leafo\ScssPhp\Compiler;

class Compile extends Node {
	private $compiler;
	
	public function init() {
		$this->compiler = new Compiler();
		$this->compiler->addImportPath(Core::join('modules', 'core', 'config'));
		$this->compiler->addImportPath(Core::join('nodes', 'core', 'config'));
		$this->compiler->setFormatter('Leafo\ScssPhp\Formatter\Crunched');
	}
	
	
	public function main() {
		$compiledFiles = []; // TODO: Actually compile stuff.
		
		echo '<h3>Success. Compiled files:</h3>';
		foreach ($compiledFiles as $file) {
			$pieces = explode('/', $file);
			echo '<p> &nbsp; &nbsp; ' . $pieces[count($pieces) - 3] . '/' . end($pieces) . '</p>';
		}
	}
	
	
	
	
	private function spider($path) {
		$modules = Core::readdir($path); // modules and nodes, but just call it modules..
		$compiledFiles = [];
		
		foreach ($modules as $module) {
			$cssPath = Core::join($path, $module, 'css');
			$cssFiles = Core::readdir($cssPath);
			
			foreach ($cssFiles as $cssFile) {
				$cssFilePath = Core::join($cssPath, $cssFile);
				$result = $this->compiler->compile(file_get_contents($cssFilePath));
				
				$targetPath = Core::join('cache', 'css', $module);
				Core::ensureDir($targetPath);
				
				$targetFile = Core::join($targetPath, substr($cssFile, 0, strpos($cssFile, '.')) . '.css');
				file_put_contents($targetFile, $result);
				$compiledFiles[] = $cssFilePath;
			}
		}
		return $compiledFiles;
	}
}
