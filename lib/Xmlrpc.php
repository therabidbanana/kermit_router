<?php

class Xmlrpc extends Kermit_Module{
	function serve(){
		$this->core->load('vendor/xmlrpc_lib');
		$s = new xmlrpc_server(
			array(
				"test.test" => array(
					"function" => "Xmlrpc::test_xmlrpc"
				),
				"test.testdb" => array(
					"function" => "Xmlrpc::test_db"
				)
			), false
		);
		$s->functions_parameters_type = 'phpvals';
		$s->service();
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