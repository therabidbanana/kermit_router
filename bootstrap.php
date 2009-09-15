<?php


// Define this dir as KERMIT_ROOT
define('__KERMIT_ROOT__', dirname(__FILE__));

// Define config dir as KERMIT_ROOT
define('__KERMIT_CONFIG__', dirname(__FILE__).'/config');

// Load the loader
require_once(__KERMIT_ROOT__ . '/lib/loader.php');
Doctrine::generateModelsFromDb('models');