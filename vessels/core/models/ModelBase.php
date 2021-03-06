<?php

namespace core\models;

class ModelBase {
	protected $data = [];
	
	public function __call($key, $params) {
		if (count($params)) {
			$this->data[$key] = $params[0];
		}
		
		if (!array_key_exists($key, $this->data)) return null;
		
		return $this->data[$key];
	}
}
