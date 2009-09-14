<?php

/* The XMLRPC server for Kermit */


include_once('lib/loader.php');

function test_xmlrpc(){
	$foo = date("D M j G:i:s T Y");
	return array(
		'name' => 'Kermit Router',
		'time' => $foo
	);
}

$s = new xmlrpc_server(
		array(
			"test.test" => array(
				"function" => "test_xmlrpc"
			)
		), 
	false);

$s->functions_parameters_type = 'phpvals';
$s->service();