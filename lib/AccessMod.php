<?php

class AccessMod extends Kermit_Module{
	const BLOCK = 0;
	const LOW = 1;
	const NORMAL = 2;
	const HIGH = 3;
	public function whenReady($module){
		if($module == 'xmlrpc'):
			$this->xmlrpc->add('access.list', get_class($this), 'access_list');
			$this->xmlrpc->add('access.blockIp', get_class($this), 'blockIp');
			$this->xmlrpc->add('access.unblockIp', get_class($this), 'unblockIp');
			$this->xmlrpc->add('access.throttleIp', get_class($this), 'throttleIp');
			$this->xmlrpc->add('access.unthrottleIp', get_class($this), 'unthrottleIp');
			$this->xmlrpc->add('access.prioritizeIp', get_class($this), 'prioritizeIp');
			$this->xmlrpc->add('access.unprioritizeIp', get_class($this), 'unprioritizeIp');
		endif;
	}
	
	public static function access_list(){
		$all = Doctrine_Query::create()
			->from('Access')
			->setHydrationMode(Doctrine::HYDRATE_ARRAY)
			->execute();
		return array('access_rules' => $all);
	}
	
	public static function blockIp($ip){
		// Delete all previous rules for IP		
		$q = Doctrine_Query::create()
			->delete('Access')
			->where('ip = ?', $ip);
		$q->execute();
		
		// Create new rule.
		$a = new Access();
		$a->ip = $ip;
		$a->level = AccessMod::BLOCK;
		$a->is_host = true;
		$a->save();
		return array('message' => "$ip was blocked");
	}
	
	public static function unblockIp($ip){
		// Delete all previous rules for IP		
		$q = Doctrine_Query::create()
			->delete('Access')
			->where('ip = ?', $ip);
		$q->execute();
		return array('message' => "$ip was unblocked");
	}
	
	public static function throttleIp($ip){
		// Delete all previous rules for IP		
		$q = Doctrine_Query::create()
			->delete('Access')
			->where('ip = ?', $ip);
		$q->execute();
		
		// Create new rule.
		$a = new Access();
		$a->ip = $ip;
		$a->level = AccessMod::LOW;
		$a->is_host = true;
		$a->save();
		return array('message' => "$ip was throttled");
	}
	
	public static function unthrottleIp($ip){
		// Delete all previous rules for IP		
		$q = Doctrine_Query::create()
			->delete('Access')
			->where('ip = ?', $ip);
		$q->execute();
		return array('message' => "$ip was unthrottled");
	}
	
	public static function throttleService($service){
	
	}
	
	public static function unthrottleService($service){
	
	}
	
	public static function prioritizeIp($ip){
		// Delete all previous rules for IP		
		$q = Doctrine_Query::create()
			->delete('Access')
			->where('ip = ?', $ip);
		$q->execute();
		
		// Create new rule.
		$a = new Access();
		$a->ip = $ip;
		$a->level = AccessMod::HIGH;
		$a->is_host = true;
		$a->save();
		return array('message' => "$ip was prioritized");
	}
	
	public static function unprioritizeIp($ip){
		// Delete all previous rules for IP		
		$q = Doctrine_Query::create()
			->delete('Access')
			->where('ip = ?', $ip);
		$q->execute();
		return array('message' => "$ip was unprioritized");
	}
	
	public static function prioritizeService($service){
	
	}
	
	public static function unprioritizeService($service){
	
	}
	public function blocked(){
		$list = $this->access_list();
		$list = $list['access_rules'];
		$blocks = array();
		foreach($list as $rule):
			if($rule['level'] == AccessMod::BLOCK) $blocks[] = $rule['ip'];
		endforeach;
		$script = "";
		foreach($blocks as $blocked):
			$host = Doctrine::getTable('Host')->findOneByIp($blocked);
			$script .= $host->mac . "\n";
		endforeach;
		return $script;
	}
	
	public function script(){
		$list = $this->access_list();
		$list = $list['access_rules'];
		$script = "#!/bin/sh\n";
		$throttles = array();
		$priorities = array();
		foreach($list as $rule):
			if($rule['level'] == AccessMod::LOW) $throttles[] = $rule['ip'];
			if($rule['level'] == AccessMod::HIGH) $priorities[] = $rule['ip'];
		endforeach;
		
		$script .= "tc qdisc del dev br0 root\n";
		$script .= "tc qdisc add dev br0 root handle 1: prio\n";
		$script .= "tc qdisc add dev br0 parent 1:1 handle 10: sfq\n";
		$script .= "tc qdisc add dev br0 parent 1:2 handle 20: sfq\n";
		$script .= "tc qdisc add dev br0 parent 1:3 handle 30: sfq\n";

		foreach($priorities as $priority):
			$script .= "tc filter add dev br0 protocol ip parent 1: prio 1 u32 match ip src $throttle flowid 1:1\n";
		endforeach;
		foreach($throttles as $throttle):
			$script .= "tc filter add dev br0 protocol ip parent 1: prio 3 u32 match ip src $throttle flowid 1:3\n";
		endforeach;
		
		$script .= "tc filter add dev br0 protocol ip parent 1: prio 2 flowid 1:2\n";
		return $script;
	}
}