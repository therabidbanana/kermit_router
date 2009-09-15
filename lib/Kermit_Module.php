<?php

class Kermit_Module{
	protected $kermit;
	public function __construct($kermit_new){
		$this->kermit = $kermit_new;
		$this->afterLoad();
	}
	public function __get($name){
		return $this->kermit->$name;
	}
	public function afterLoad(){
		return true;
	}
}