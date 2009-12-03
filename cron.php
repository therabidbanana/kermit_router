<html>
<head>
<title>Cron</title>
<!-- <meta http-equiv="refresh" content="10" /> -->
</head>
<body>
	<p>Executing...</p>
	<?php 
		include_once('bootstrap.php');
		History::dataPoint(false); 
		if(intval(date('i')) == 0 || intval(date('i')) == 30) Speed::upAndDown(false);
	?>
</body>
</html>