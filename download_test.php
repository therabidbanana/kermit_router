<?php
sleep(2); // Throttle for testing.

header("Content-Type: text/plain");
srand(time());
$file_size = 1024; // in kB

// Filesize from get variable
if(isset($_GET['size']) && intval($_GET['size'] < 4096)) 
	$file_size = intval($_GET['size']); 

// Seed value from get variable
if(isset($_GET['seed'])) 
	srand(time()+intval($_GET['seed'])); 
	
for($i = 0; $i < (32*$file_size); $i++){
	echo md5(rand());
}