<?php

class Speed extends Kermit_Module{
	public function whenReady($module){
		if($module == 'xmlrpc'):
			$this->xmlrpc->add('speed.download', get_class($this), 'download');
			$this->xmlrpc->add('speed.upload', get_class($this), 'upload');
			$this->xmlrpc->add('speed.downloadMbps', get_class($this), 'downloadMbps');
			$this->xmlrpc->add('speed.uploadMbps', get_class($this), 'uploadMbps');
			$this->xmlrpc->add('speed.upAndDown', get_class($this), 'upAndDown');
			$this->xmlrpc->add('speed.history', get_class($this), 'history');
		endif;
	}
	
	public function downloadSpeed(){
		$url = 'http://www-dev.research.cc.gatech.edu/projects/kermit/bandwidth_tests/';
		$file = 'download_test.php';
		$seed  = md5(time());
		srand(time());
		// size in kB
		$size = 7*1024 + rand(0,1024);
		$options = "?size=$size&seed=$seed";
		//$temp = __KERMIT_ROOT__ . '/tmp/curl_'.$seed.'.txt';
		
		$ch = curl_init($url.$file.$options);
		//$fp = fopen($temp, 'w');
		//curl_setopt($ch, CURLOPT_FILE, $fp);
		set_time_limit(190);
		curl_setopt($ch,CURLOPT_TIMEOUT, 180); // Set timeout. Don't want to go more than three minutes.
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
		$url = 'http://www-dev.research.cc.gatech.edu/projects/kermit/bandwidth_tests/';
		$file = 'upload_test.php';
		$seed  = time();
		srand($seed);
		$size = 2*1024 + rand(0,1024);
		$temp = __KERMIT_ROOT__ . '/'.'tmp'.'/'.'curl_'.$seed.'.txt';
		
		
		$ch = curl_init($url.$file);
		
		$fp = fopen($temp, 'w');
			
		for($i = 0; $i < (32*$size); $i++){
			fwrite($fp, md5(rand()));
		}
		fclose($fp);
		set_time_limit(190);
		$data = array('file' => '@'.$temp);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_TIMEOUT, 180); // Set timeout. Don't want to go more than three minutes.
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
		try{
			$avg = $kermit->speed->downloadSpeed();
		}
		catch(Exception $e){
			$avg = 0;
		}
		return $avg;
	}
	
	// Returns a megabit per second count
	public static function downloadMbps(){
		global $kermit;
		try{
			$avg = $kermit->speed->downloadSpeed();
		}
		catch(Exception $e){
			$avg = 0;
		}
		return ($avg / (1024*1024)) * 8; // In Mbps
	}
	
	// Returns a megabit per second count
	public static function upAndDown($log = true){
		global $kermit;
		try{
			$avg = $kermit->speed->uploadSpeed();
		}
		catch(Exception $e){
			$avg = 0;
		}
		$up = ($avg / (1024*1024)) * 8;
		
		try{
			$avg = $kermit->speed->downloadSpeed();
		}
		catch(Exception $e){
			$avg = 0;
		}
		$down = ($avg / (1024*1024)) * 8;
		
		$ret = array('up' => $up, 'down' => $down); // In Mbps
		if($log) $kermit->xmlrpc->log('speed.upAndDown', "Speed up/down: ( $up / $down ) mbps", $ret);
		$sl = new SpeedLog();
		$sl->up_mbps = $up;
		$sl->down_mbps = $down;
		$sl->save();
		return $ret;
	}
	
	public static function history(){
		global $kermit;
		$q = Doctrine_Query::create()
			 ->from('SpeedLog')
			 ->orderBy('created_at ASC')
			 ->execute();
		$history = $q->toArray();
		$kermit->xmlrpc->log('speed.history', "Getting speed history", $history);
		return array('history' => $history);
	}
	
	// Returns a bytes per second count
	public static function upload(){
		global $kermit;
		try{
			$avg = $kermit->speed->uploadSpeed();
		}
		catch(Exception $e){
			$avg = 0;
		}
		return $avg;
	}
	
	// Returns a megabit per second count
	public static function uploadMbps(){
		global $kermit;
		try{
			$avg = $kermit->speed->uploadSpeed();
		}
		catch(Exception $e){
			$avg = 0;
		}
		return ($avg / (1024*1024)) * 8; // In Mbps
	}
}