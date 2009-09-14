<?php

class Database extends Kermit_Module{
	public function __construct($kermit){
		$this->kermit = $kermit;
		// Load Doctrine Core
		$this->kermit->load('vendor/Doctrine');
		// Register autoloader for Doctrine
		spl_autoload_register(array('Doctrine', 'autoload'));
	}
}