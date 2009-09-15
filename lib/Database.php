<?php

class Database extends Kermit_Module{
	public function afterLoad(){
		// Load Doctrine Core
		$this->core->load('vendor/Doctrine');
		// Register autoloader for Doctrine
		spl_autoload_register(array('Doctrine', 'autoload'));
	}
}