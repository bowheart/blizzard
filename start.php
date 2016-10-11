<?php

ini_set('display_errors', 1);

use core\Router;

/**
 * For, e.g. $class="core\Router", $path="vessels"
 */
function autoload($class, $typePrefix) {
	$pieces = explode('\\', $class);
	
	// Look for 'vessels/core/Router.php'
	$path = $typePrefix . implode('/', $pieces) . '.php';
	if (file_exists($path)) return require_once $path;
	
	// Look for 'vessels/core/router/Router.php'
	$classFile = array_pop($pieces);
	$pieces[] = strtolower(substr(preg_replace('([A-Z])', '-$0', $classFile), 1)); // Pascal to dash (e.g. MyFile -> my-file
	$path = $typePrefix . implode('/', $pieces) . "/$classFile.php";
	if (file_exists($path)) return require_once $path;
}


/**
 * The autoloader for vessels.
 */
spl_autoload_register(function($class) {
	autoload($class, 'vessels/');
});


/**
 * The autoloader for nodes.
 */
spl_autoload_register(function($class) {
	autoload($class, '');
});


/**
 * The autoloader for composer packages.
 */
require_once 'vendor/autoload.php';


parse_str(implode('&', array_slice(isset($argv) ? $argv : [], 1)), $_GET);
Router::route();
