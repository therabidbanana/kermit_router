<?php

class Flex extends Kermit_Module{
	public function latest(){
		$dir = __KERMIT_ROOT__ . '/files';
		$handle = @opendir($dir);
		$max = 0;
		$maxfile = '';
		$error = false;
		if(empty($handle)):
			$error = true;
		else:
			while(false !== ($file = readdir($handle))):
				if(is_file($dir . '/'. $file)):
					$timestamp = explode('-', $file); 
					$timestamp = $timestamp[0];
					if(intval($timestamp) > $max && strpos( $file, '.swf') !== false){
						$max = intval($timestamp);
						$maxfile = $file;
					}
				endif;
			endwhile;
		endif;
		
		closedir($handle);
		return '/files'.$maxfile;
	}
}