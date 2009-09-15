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
		$this->load('Config', 'config');
	}
	
	function load($lib, $name = false){
		if(!isset($this->modules[$name])){
			require_once(__KERMIT_LIB__ . '/' . $lib . '.php');
			if($name) $this->modules[$name] = new $lib($this);
		}
	}
	
	function load_modules(){
		$mods = $this->config->get('modules');
		if(isset($mods['vendor'])){
			foreach($mods['vendor'] as $mod): 
				$this->load('vendor'.'/'.$mod);
			endforeach;
		}
		if(isset($mods['kermit'])){
			foreach($mods['kermit'] as $mod => $name):
				$this->load($mod, $name);
			endforeach;
		}
	}
	
	function __get($name){
		return $this->modules[$name];
	}
}