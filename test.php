<?php
function test_table($mysqli, $table, $columns){
	$result = $mysqli->query('SELECT '.$columns . ' FROM '. $table. " LIMIT 1");
	$table = '`'.$table.'`';
	while(strlen($table) < 17) $table = $table . ' ';
	echo "\t\tIs $table there? \tYes. \t\t\t\t\t".($result ? '[OK]'."\n" : die("\n\nFAIL: table $table not found or does not match schema.") );
}

echo '<pre>';
echo "Checking kermit environment...\n\n";

// Curl Check
echo "Checking to see what version of curl is available to PHP... ";
$vers = curl_version();
echo $vers['version'] . " \t\t\t[OK]\n";
echo "\n";

// 
echo "Checking to see if mysql set up... ";
$mysqli = new mysqli('localhost', 'root', 'work2play', 'rflow') or die("\n\nFAIL: could not connect to mysql://root:work2play@localhost");
echo "Connected \t\t\t\t\t\t[0K]\n";
echo "...\tChecking to make sure old_password set for rflow...";
@mysql_connect('localhost', 'rflow', 'work2play') and die("\n\nFAIL: no error thrown connecting to user rflow@localhost, old_password not set");
echo "\t\t\t\t[OK]\n";
echo "...\tChecking to make sure the tables are there...\n";
test_table($mysqli, 'access', 'id, is_host, ip, service, level');
test_table($mysqli, 'akteth', 'ip, mac, status, lasttraffic, name, device');
test_table($mysqli, 'aktrouter', 'ip, flowsequenz, lastflow, ploss');
test_table($mysqli, 'kermit_host', 'id, mac, status, name, allowed');
test_table($mysqli, 'log', 'id, xmlrpc_call, xmlrpc_return, args, message');
test_table($mysqli, 'speed', 'id, up_mbps, down_mbps, created_at');
test_table($mysqli, 'traffic', 'ID, zeit, name, datum, uloktets');
test_table($mysqli, 'traffic_history', 'id, start_time, end_time, up, ip, down, up_avg, down_avg');
test_table($mysqli, 'wlanclients', 'mac, ip, rssi, location, status');
$res = $mysqli->query('SELECT DISTINCT mac, COUNT(mac) as `count` FROM akteth');
if(!$res) trigger_error('Error running query. '.$mysqli->error,E_USER_ERROR);

while($mac = $res->fetch_array(MYSQLI_ASSOC)):
	if($mac['count'] > 1) die("\nFAIL: There are duplicate MAC addresses in the rflow table. This will cause massive failures.");
endwhile;
$mysqli->close();
echo "\n";


echo '</pre>';

echo "<h3>The Kermit environment seems ready to go.</h3>";