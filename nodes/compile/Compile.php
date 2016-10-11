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
		$this->compiler->addImportPath(Core::join('modules', 'core'));
		$this->compiler->addImportPath(Core::join('modules', 'core', 'modules', 'base'));
		$this->compiler->addImportPath(Core::join('nodes', 'core'));
		$this->compiler->setFormatter('Leafo\ScssPhp\Formatter\Crunched');
	}
	
	
	public function main() {
		$compiledFiles = $this->spider('nodes'); // TODO: Actually compile stuff.
		$compiledFiles = array_merge($compiledFiles, $this->spider('modules'));
		
		echo '<h3>Success. Compiled files:</h3>';
		foreach ($compiledFiles as $file) {
			echo '<p> &nbsp; &nbsp; ' . $file . '</p>';
		}
	}
	
	
	
	
	private function spider($path) {
		$files = Core::readdir($path);
		$compiledFiles = [];
		
		foreach ($files as $file) {
			$filePath = Core::join($path, $file);
			
			if (is_dir($filePath)) {
				$compiledFiles = array_merge($compiledFiles, $this->spider($filePath));
				continue;
			}
			
			// It's a file. Look for .scss files.
			if (substr($file, -5) !== '.scss') continue;
			
			// It's a .scss file. Compile it.
			$compiledSass = $this->compiler->compile(file_get_contents($filePath));
			
			// Create a .css file in the cache/ directory for the compiled sass.
			$currentPathPieces = explode('/', $filePath);
			$graphType = array_shift($currentPathPieces);
			$fileName = array_pop($currentPathPieces);
			
			if (substr($fileName, 0, 1) === '_') continue; // don't compile config sass files (starting with '_')
			
			$targetFileName = preg_replace('/\.scss/', '.css', $fileName);
			$targetPathPieces = array_filter($currentPathPieces, function($piece) use ($graphType) {
				return $piece !== $graphType;
			});
			$cachePath = Core::join($graphType, implode('/', $targetPathPieces));
			$targetPath = Core::join('cache', 'css', $cachePath);
			
			Core::ensureDir($targetPath);
			file_put_contents(Core::join($targetPath, $targetFileName), $compiledSass);
			$compiledFiles[] = Core::join($cachePath, $fileName);
		}
		
		return $compiledFiles;
	}
}
