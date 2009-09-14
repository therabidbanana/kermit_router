<?php

/* 
 * The Kermit Core
 * Handles loading of Kermit necessities, makes them globally accessible.
 */ 
class Kermit{
	protected $modules;
	public function __construct(){
		$modules = array();
	}
	
	function load($lib, $name = false){
		require_once(__KERMIT_LIB__ . '/' . $lib . '.php');
		if($name) $modules[$name] = new $lib($this);
	}
}