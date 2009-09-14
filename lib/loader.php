<?php

// Define this dir as KERMIT_LIB
define('__KERMIT_LIB__', dirname(__FILE__));
// Load the Kermit class
require_once(dirname(__FILE__) . '/Kermit.php');

$kermit = new Kermit();
$kermit->load('Database', 'db');