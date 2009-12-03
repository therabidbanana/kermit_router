<?php

/* The XMLRPC server for Kermit */


include_once('bootstrap.php');
set_error_handler("handle_error");
$kermit->xmlrpc->serve();
function handle_error($errno, $errstring) {
	$msg = $errstring;
    echo "<?xml version='1.0'?>
<methodResponse>
<fault>
<value>
<struct><member><name>faultCode</name>
<value><int>1337</int></value>
</member>
<member>
<name>faultString</name>
<value><string>PHP error: $msg</string></value>
</member>
</struct>
</value>

</fault>
</methodResponse>";
	die();
}
