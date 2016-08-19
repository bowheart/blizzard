<?php

ini_set('display_errors', 1);

use core\Router;


function autoload($class, $type) {
	$pieces = explode('\\', $class);
	$classFile = array_pop($pieces);
	
	$pieces[] = substr(preg_replace('([A-Z])', '-$0', $classFile), 1); // Pascal to dash (e.g. MyFile -> my-file
	$pieces = array_map('strtolower', $pieces); // make all the pieces lower-case
	
	if ($type === 'nodes') array_shift($pieces); // get rid of the 'nodes' piece
	
	$path = "$type/" . implode($pieces, '/vessels/') . "/$classFile.php";
	
	if (file_exists($path)) require_once $path;
};


/**
 * The autoloader for vessels.
 */
spl_autoload_register(function($class) {
	autoload($class, 'vessels');
});


/**
 * The autoloader for nodes.
 */
spl_autoload_register(function($class) {
	autoload($class, 'nodes');
});


Router::route();
