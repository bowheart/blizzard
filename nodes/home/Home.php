<?php

namespace nodes;

use core\models\Node;

class Home extends Node {
	public function main() {
		$this->app('app');
	}
}
