<?php

include "bootstrap.php";

$mod = $_GET['mod'];
$func = $_GET['func'];
$arg = $_GET['arg'];
echo '<pre>';
if(isset($_GET['arg'])) print_r($kermit->$mod->$func($arg));
else print_r($kermit->$mod->$func());
echo '</pre>';