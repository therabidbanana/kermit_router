<?php

class Database{
	public function __construct($kermit){
		// Load Doctrine Core
		$kermit->load('vendor/Doctrine');
		// Register autoloader for Doctrine
		spl_autoload_register(array('Doctrine', 'autoload'));
	}
}