<?php

include "bootstrap.php";

$mod = $_GET['mod'];
$func = $_GET['func'];

print_r($kermit->$mod->$func());