<?php

class Database extends Kermit_Module{
	public $connection;
	public function afterLoad(){
		// Load Doctrine Core
		$this->core->load('vendor/Doctrine');
		// Register autoloader for Doctrine
		spl_autoload_register(array('Doctrine', 'autoload'));
		$config = $this->config->get('dbconfig');
		$active = $config['active_connection'];
		$this->connection = Doctrine_Manager::connection($config['connections'][$active]);
		Doctrine::loadModels(__KERMIT_ROOT__. '/'. 'models');
	}
}