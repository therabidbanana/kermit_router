<?php

class Config extends Kermit_Module{
	public $config;
	
	public function afterLoad(){
		$this->config = array();
		$this->core->load('Parser', 'parser');
	}
	public function load($file){
		$arr =  $this->parser->yaml(__KERMIT_CONFIG__ . '/' . $file . '.yaml');
		$this->config[$file] = $arr;
		return $arr;
	}
	
	public function get($file){
		if(isset($this->config[$file])) return $this->config[$file];
		else return $this->load($file);
	}
}