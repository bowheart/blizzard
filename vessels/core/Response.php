<?php

namespace core;

use Core;
use core\Router;

class Response {
	public static function badAuth() {
		http_response_code(401);
		die();
	}
	
	public static function data($data) {
		$data = Core::parseModel($data);
		echo json_encode($data);
		die();
	}
	
	public static function error($message = '') {
		http_response_code(400);
		die($message ? $message : 'Bad Request');
	}
	
	public static function notFound($message = '') {
		http_response_code(404);
		die($message ? $message : 'Not Found');
	}
	
	public static function notFoundPage() {
		Router::serveNode('not-found');
		die();
	}
	
	public static function ok($text = '') {
		if ($text) echo $text;
		http_response_code(200);
		die();
	}
	
	public static function read($mimeType, $file) {
		header('Content-Type: ' . $mimeType);
		readfile($file);
		die();
	}
	
	public static function redirect($target) {
		http_response_code(302);
		header('Location: ' . $target);
		die();
	}
	
	public static function renderData($title, $data) {
		echo '<h2>' . $title . '</h2>';
		print_r($data);
		die();
	}
}
