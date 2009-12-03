<?php

class Who extends Kermit_Module{
	public function whenReady($module){
		if($module == 'xmlrpc'):
			$this->xmlrpc->add('who.list', get_class($this), 'who_list');
			$this->xmlrpc->add('who.setHostname', get_class($this), 'setHostname');
			$this->xmlrpc->add('who.setStatus', get_class($this), 'setStatus');
			$this->xmlrpc->add('who.setImage', get_class($this), 'setImage');
			$this->xmlrpc->add('who.amI', get_class($this), 'amI');
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
			$stats = $kermit->history->lastStatsForIp($host->ip);
			$host_up = $stats['up'];
			$host_down = $stats['down'];
			$host_up_avg = $stats['up_avg'];
			$host_down_avg = $stats['down_avg'];
			
			if(($host->status && !$kerm->allowed) || $kerm->allowed):
				$ret['hosts'][] = array('ip' => $host->ip, 'recent_activity' => $host->status, 
										'status' => $kerm->status,
										'wireless' => !$wired, 'hostname' => $kerm->name, 'recognized' => $kerm->allowed,
										'id' => $kerm->id, 'up' => $host_up, 'down' => $host_down,
										'up_avg' => $host_up_avg, 'down_avg' => $host_down_avg,
										'image' => $kerm->image);
			endif;
		endforeach;
		
		$kermit->xmlrpc->log('who.list', 'Listing clients', $ret);
		return $ret;
	}
	
	public function kermitForHost($host){
		$kerm = Doctrine::getTable('KermitHost')->findOneByIp($host->ip);
		if(!$kerm){
			$kerm = new KermitHost();
			$kerm->mac = $host->mac;
			$kerm->ip = $host->ip;
			$kerm->name = gethostbyaddr($host->ip);
			$kerm->status = '';
			$kerm->allowed = 0;
			$kerm->save();
		}
		return $kerm;
	}
	
	public static function amI(){
		global $kermit;
		return $kermit->who->kermitForIp($_SERVER['REMOTE_ADDR']);
		$kermit->xmlrpc->log('who.amI', "Called whoami", array());
	}
	
	public function kermitForIp($ip){
		$kerm = Doctrine::getTable('KermitHost')->findOneByIp($ip);
		return ($kerm ? $kerm->toArray() : false);
	}
	
	public static function setHostname($id, $name){
		global $kermit;
		$kerm = Doctrine::getTable('KermitHost')->findOneById($id);
		if($kerm):
			$kerm->name = $name;
			// Assume the client is known now, since we're giving it a name.
			$kerm->allowed = 1;
			$kerm->save();
			$ret = array('status' => 'success', 'error' => 0, 'message' => 'Hostname successfully updated to '.$name);
			$kermit->xmlrpc->log('who.setHostname', "Changed kermit id #$id's name to $name", $ret, array('id' => $id, 'name' => $name));
			return $ret;
		else:
			$kermit->xmlrpc->log('who.setHostname', "Error changing kermit id #$id's name", array(), array('id' => $id, 'name' => $name));
			return array('status' => 'error', 'error' => 1, 'message' => 'Host could not be found');
		endif;
	}
	
	public static function setImage($id, $src){
		global $kermit;
		$kerm = Doctrine::getTable('KermitHost')->findOneById($id);
		if($kerm):
			$kerm->image = $src;
			// Assume the client is known now, since we're giving it an image.
			$kerm->allowed = 1;
			$kerm->save();
			$my_return = array('status' => 'success', 'error' => 0, 'message' => 'Image successfully updated to '.$src);
			$kermit->xmlrpc->log('who.setImage', "#$id's image to $src");
			return $my_return;
		else:
			$kermit->xmlrpc->log('who.setImage', "Error changing kermit id #$id's image", array(), array('id' => $id, 'src' => $src));
			return array('status' => 'error', 'error' => 1, 'message' => 'Host could not be found');
		endif;
	}
	
	public static function setStatus($id, $status){
		global $kermit;
		$kerm = Doctrine::getTable('KermitHost')->findOneById($id);
		if($kerm):
			$kerm->status = $status;
			// Assume the client is known now, since we're giving it a status.
			$kerm->allowed = 1;
			$kerm->save();
			$my_return = array('status' => 'success', 'error' => 0, 'message' => 'Status successfully updated to '.$status);
			$kermit->xmlrpc->log('who.setStatus', "#$id's status to $status", $my_return, array('id' => $id, 'status' => $status));
			return $my_return;
		else:
			$kermit->xmlrpc->log('who.setStatus', "Error changing kermit id #$id's status", array(), array('id' => $id, 'status' => $status));
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