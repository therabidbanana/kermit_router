<?php

/* 
 * The Kermit Core
 * Handles loading of Kermit necessities, makes them globally accessible.
 */ 
class Kermit{
	protected $modules;
	public function __construct(){
		$this->modules = array();
		$this->modules['core'] = $this;
		$this->load('Kermit_Module');
	}
	
	function load($lib, $name = false){
		require_once(__KERMIT_LIB__ . '/' . $lib . '.php');
		if($name) $this->modules[$name] = new $lib($this);
	}
	
	function __get($name){
		return $this->modules[$name];
	}
}