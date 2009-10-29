<?php

	if(!isset($_FILES['file']['name'])):
?>
		<form enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
			<input type="file" name="file" />
			<input type="submit" value="Go!" />
		</form>
<?php 
	else: 
		$name = basename($_FILES['file']['name']);
		// echo $name;
		$new_name = 'images/'.$name;
		
		$result = @move_uploaded_file($_FILES['file']['tmp_name'], $new_name);
		if(empty($result)) echo '0';
		else echo '1';
	endif;