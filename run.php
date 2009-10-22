<?php

include "bootstrap.php";

$mod = $_GET['mod'];
$func = $_GET['func'];
echo '<pre>';
print_r($kermit->$mod->$func());
echo '</pre>';