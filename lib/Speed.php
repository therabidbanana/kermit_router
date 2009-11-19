<?php

class Speed extends Kermit_Module{
	public function whenReady($module){
		if($module == 'xmlrpc'):
			$this->xmlrpc->add('speed.download', get_class($this), 'download');
			$this->xmlrpc->add('speed.upload', get_class($this), 'upload');
			$this->xmlrpc->add('speed.downloadMbps', get_class($this), 'downloadMbps');
			$this->xmlrpc->add('speed.uploadMbps', get_class($this), 'uploadMbps');
		endif;
	}
	
	public function downloadSpeed(){
		$url = 'http://kermit.local/';
		$file = 'download_test.php';
		$seed  = md5(time());
		srand(time());
		$size = 4*1024 + rand(0,1024);
		$options = "?size=$size&seed=$seed";
		//$temp = __KERMIT_ROOT__ . '/tmp/curl_'.$seed.'.txt';
		
		$ch = curl_init($url.$file.$options);
		//$fp = fopen($temp, 'w');
		//curl_setopt($ch, CURLOPT_FILE, $fp);    
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // No need to save to temp file, use in memory instead
		$before = microtime(true);
		curl_exec($ch);
		$after = microtime(true);
		curl_close($ch);
		//fclose($fp);
		//if(is_file($temp)) unlink($temp);
		
		$avg = ($size*1024) / ($after - $before);
		return $avg; // In bytes / sec
	}
	
	public function uploadSpeed(){
		$url = 'http://kermit.local/';
		$file = 'upload_test.php';
		$seed  = time();
		srand($seed);
		$size = 4*1024 + rand(0,1024);
		$temp = __KERMIT_ROOT__ . '/'.'tmp'.'/'.'curl_'.$seed.'.txt';
		
		
		$ch = curl_init($url.$file);
		
		$fp = fopen($temp, 'w');
			
		for($i = 0; $i < (32*$size); $i++){
			fwrite($fp, md5(rand()));
		}
		fclose($fp);
		
		$data = array('file' => '@'.$temp);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$before = microtime(true);
		curl_exec($ch);
		$after = microtime(true);
		curl_close($ch);
		
		
		if(is_file($temp)) unlink($temp);
		
		$avg = ($size*1024) / ($after - $before);
		return $avg; // In bytes / sec
	}
	
	// Returns a bytes per second count
	public static function download(){
		global $kermit;
		return $kermit->speed->downloadSpeed();
		
	}
	
	// Returns a megabit per second count
	public static function downloadMbps(){
		global $kermit;
		$avg = $kermit->speed->downloadSpeed();
		return ($avg / (1024*1024)) * 8; // In Mbps
	}
	
	// Returns a bytes per second count
	public static function upload(){
		global $kermit;
		return $kermit->speed->uploadSpeed();
		
	}
	
	// Returns a megabit per second count
	public static function uploadMbps(){
		global $kermit;
		$avg = $kermit->speed->uploadSpeed();
		return ($avg / (1024*1024)) * 8; // In Mbps
	}
}