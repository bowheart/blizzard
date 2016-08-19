<?php

use core\Request;
use core\Response;

class Node {
	private $method;

	
	public function __construct() {
		$method = $this->method = Core::defaultVal(Request::next(), 'main');
		
		$this->init();
		$this->bufferMethod($method);
	}
	
	
	
	
	
	protected function init() {} // No-op. Meant to be overridden.
	
	protected function nodeClass() {
		return get_class($this);
	}
	
	protected function nodeName() {
		$classNodes = explode('\\', $this->nodeClass());
		return array_pop($classNodes);
	}
	
	protected function nodePath() {
		$reflector = new \ReflectionClass(get_called_class());
		return dirname($reflector->getFileName());
	}
	
	
	
	
	
	private function bufferMethod($method) {
		if (!method_exists($this, $method) || !is_callable([$this, $method])) Response::notFound();
		
		ob_start();
		$this->{$method}();
		$content = ob_get_contents();
		ob_end_clean();
		
		$this->render($content);
	}
	
	private function render($output) { ?>
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width,initial-scale=1">
			<title><?= ucfirst($this->nodeName()) . ($this->method !== 'main' ? ' | ' . ucfirst($this->method) : '') ?></title>
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
			<link rel="stylesheet" href="/css/base/base.css">
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
			<script>window.blizzard = {user: {}}</script>
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
			<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
			<script src="/js/base/base.js"></script>
		</head>
		<body>
			<?= trim($output) ? $output : 'TODO: put scripts here' ?>
		</body>
		</html>
	<?php }
}
