<?php

class Who extends Kermit_Module{
	public function whenReady($module){
		if($module == 'xmlrpc'):
			$this->xmlrpc->add('who.list', get_class($this), 'who_list');
			$this->xmlrpc->add('who.setHostname', get_class($this), 'setHostname');
			$this->xmlrpc->add('who.setStatus', get_class($this), 'setStatus');
			$this->xmlrpc->add('who.getImages', get_class($this), 'getImages');
		endif;
	}
	
	public static function who_list(){
		global $kermit;
		$hosts = Doctrine::getTable('Host')->findAll();
		$ret = array('hosts' => array());
		foreach($hosts as $host):
			$wireless = Doctrine::getTable('Wlanclients')->findOneByMac($host->mac);
			if($wireless) $wired = false;
			else $wired = true;
			
			$kerm = $kermit->who->kermitForHost($host);
			$host_up = $kermit->history->lastUpForIp($host->ip);
			$host_down = $kermit->history->lastDownForIp($host->ip);
			$host_up_avg = $kermit->history->lastUpAvgForIp($host->ip);
			$host_down_avg = $kermit->history->lastDownAvgForIp($host->ip);
			if(($host->status && !$kerm->allowed) || $kerm->allowed):
				$ret['hosts'][] = array('ip' => $host->ip, 'recent_activity' => $host->status, 
										'status' => $kerm->status,
										'wireless' => !$wired, 'hostname' => $kerm->name, 'recognized' => $kerm->allowed,
										'id' => $kerm->id, 'up' => $host_up, 'down' => $host_down,
										'up_avg' => $host_up_avg, 'down_avg' => $host_down_avg);
			endif;
		endforeach;
		return $ret;
	}
	
	public function kermitForHost($host){
		$kerm = Doctrine::getTable('KermitHost')->findOneByMac($host->mac);
		if(!$kerm){
			$kerm = new KermitHost();
			$kerm->mac = $host->mac;
			$kerm->name = gethostbyaddr($host->ip);
			$kerm->status = '';
			$kerm->allowed = 0;
			$kerm->save();
		}
		return $kerm;
	}
	
	public static function setHostname($id, $name){
		$kerm = Doctrine::getTable('KermitHost')->findOneById($id);
		if($kerm):
			$kerm->name = $name;
			// Assume the client is known now, since we're giving it a name.
			$kerm->allowed = 1;
			$kerm->save();
			return array('status' => 'success', 'error' => 0, 'message' => 'Hostname successfully updated to '.$name);
		else:
			return array('status' => 'error', 'error' => 1, 'message' => 'Host could not be found');
		endif;
	}
	
	public static function setStatus($id, $status){
		$kerm = Doctrine::getTable('KermitHost')->findOneById($id);
		if($kerm):
			$kerm->status = $status;
			// Assume the client is known now, since we're giving it a status.
			$kerm->allowed = 1;
			$kerm->save();
			return array('status' => 'success', 'error' => 0, 'message' => 'Status successfully updated to '.$status);
		else:
			return array('status' => 'error', 'error' => 1, 'message' => 'Host could not be found');
		endif;

	}
	
	public static function getImages(){	
		$dir = __KERMIT_ROOT__ . '/images';
		$handle = @opendir($dir);
		$files = array();
		$error = false;
		if(empty($handle)):
			$error = true;
		else:
			while(false !== ($file = readdir($handle))):
				if(is_file($dir . '/'. $file)):
					$files[] = $file;
				endif;
			endwhile;
		endif;
		
		closedir($handle);
		if($error) array('status' => 'error', 'message' => 'There was a problem opening the directory. Check permissions');
		else return array('status' => 'success', 'images' => $files);
	}
}