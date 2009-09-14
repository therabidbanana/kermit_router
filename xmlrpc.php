<?php

/* The XMLRPC server for Kermit */


include_once('lib/loader.php');
$kermit->load('Xmlrpc', 'xmlrpc');
$kermit->xmlrpc->serve();
