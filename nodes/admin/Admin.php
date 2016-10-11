<?php

namespace nodes;

use Node;
use User;
use core\Response;

class Admin extends Node {
	public function main() {
		if (User::is('admin')) Response::redirect('/admin/dashboard');
		Response::redirect('/admin/login');
	}
	
	public function dashboard() {
		
	}
	
	public function login() {
		if (User::is('admin')) Response::redirect('/admin/dashboard');
		$this->renderFile('login');
	}
	
	public function logout() {
		
	}
}
