<?php

class Xmlrpc extends Kermit_Module{
	protected $functions;
	
	function add($name, $class, $function){
		$this->functions[$name] = array('function' => $class . '::' . $function);
	}
	function afterLoad(){
	}
	
	function whenReady($module){
		if($module = 'xmlrpc'):
			$this->xmlrpc->add('test.test', get_class($this), 'test_xmlrpc');
			$this->xmlrpc->add('test.testdb', get_class($this), 'test_db');
			$this->xmlrpc->add('test.testfunc', get_class($this), 'test_func');
			return true;
		else:
			return true;
		endif;
	}
	
	// Calling this function loads and serves the xmlrpc interface
	function serve(){
		$this->core->load('vendor/xmlrpc_lib');
		$s = new xmlrpc_server($this->functions, false);
		$s->functions_parameters_type = 'phpvals';
		$s->service();
	}
	function test_func($something){
		return array('something' => $something);
	}
	function test_db(){
		$foo = date("D M j G:i:s T Y");
		$last = Doctrine_Query::create()
				->from('Traffic')
				->groupBy('srcip')
				->fetchOne()
				->toArray();
		return array(
			'name' => 'Kermit Router',
			'time' => $foo,
			'packet' => print_r($last, TRUE)
		);
	}
	
	function test_xmlrpc(){
		$foo = date("D M j G:i:s T Y");
		return array(
			'name' => 'Kermit Router',
			'time' => $foo
		);
	}
}