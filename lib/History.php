<?php


class History extends Kermit_Module{
	public function whenReady($module){
		if($module == 'xmlrpc'):
			$this->xmlrpc->add('history.list', get_class($this), 'history_list');
			$this->xmlrpc->add('history.last_ten', get_class($this), 'last_ten');
			$this->xmlrpc->add('history.last_hour', get_class($this), 'last_hour');
			$this->xmlrpc->add('history.last_day', get_class($this), 'last_day');
			$this->xmlrpc->add('history.data_point', get_class($this), 'data_point');
			$this->xmlrpc->add('history.lastTen', get_class($this), 'last_ten');
			$this->xmlrpc->add('history.lastHour', get_class($this), 'last_hour');
			$this->xmlrpc->add('history.lastDay', get_class($this), 'last_day');
			$this->xmlrpc->add('history.dataPoint', get_class($this), 'data_point');
		endif;
	}
	
	public static function history_list(){
		$hosts = Doctrine::getTable('Host')->findAll();
		$ret = array();
		foreach($hosts as $host):
			$history = Doctrine_Query::create()
				->from('TrafficHistory')
				->where('ip = ?', $host->ip)
				->orderBy('end_time ASC')
				->execute();
			$ret[$host->ip] = $history->toArray();
		endforeach;
		return array('history' => $ret);
	}
	
	public static function last_ten(){
		global $kermit;
		$hosts = Doctrine::getTable('Host')->findAll();
		$ret = array();
		foreach($hosts as $host):
			$ret[$host->ip] = $kermit->history->historyBlocksForIp($host->ip, time() - 10*60, 5);
		endforeach;
		return array('history' => $ret);
	}
	

	
	public static function last_hour(){
		global $kermit;
		$hosts = Doctrine::getTable('Host')->findAll();
		$ret = array();
		foreach($hosts as $host):
			$ret[$host->ip] = $kermit->history->historyBlocksForIp($host->ip, time() - 60*60, 12, 5*60);
		endforeach;
		return array('history' => $ret);
	}
	
	public static function last_day(){
		global $kermit;
		$hosts = Doctrine::getTable('Host')->findAll();
		$ret = array();
		foreach($hosts as $host):
			$ret[$host->ip] = $kermit->history->historyBlocksForIp($host->ip, time() - 24*60*60, 24, 60*60);
		endforeach;
		return array('history' => $ret);
	}
	
	public function dateRangeForIp($ip, $timestamp1, $timestamp2){
		date_default_timezone_set('America/New_York');
		$padding = 40;								// Create some extra room to work
		$timestamp1 = $timestamp1 - $padding; 
		$timestamp2 = $timestamp2 + $padding;
		$date1 = date('Y-m-d H:i:s', $timestamp1);
		$date2 = date('Y-m-d H:i:s', $timestamp2);
		$history = Doctrine_Query::create()
			->select('	MIN(start_time) as start_time, MAX(end_time) as end_time, 
						SUM(up) as up, SUM(down) as down, AVG(up_avg) as up_avg, AVG(down_avg) as down_avg')
			->from('TrafficHistory')
			->where('ip = ?', $ip)
			->andWhere('start_time > ? AND start_time < ?', array($date1, $date2))
			->andWhere('end_time < ? AND end_time > ?', array($date2, $date1))
			->orderBy('end_time ASC');
		$ret = $history->fetchOne()->toArray();
		return $ret;
	}
	
	public function historyBlocksForIp($ip, $start_time, $block_count = 12, $block_size = 120){
		$blocks = array();
		for($i = 0; $i < $block_count; $i++){
			$start = $start_time + ($i*$block_size);
			$end = $start + $block_size;
			$blocks[] = $this->dateRangeForIp($ip, $start, $end);
		}
		return $blocks;
	}
	
	public function lastUpForIp($ip){
		$ret = Doctrine_Query::create()
			->from('TrafficHistory')
			->orderBy('end_time DESC')
			->where('ip = ?', $ip)
			->limit(1)
			->fetchOne();
		return $ret['up'];
	}
	public function lastUpAvgForIp($ip){
		$ret = Doctrine_Query::create()
			->from('TrafficHistory')
			->orderBy('end_time DESC')
			->where('ip = ?', $ip)
			->limit(1)
			->fetchOne();
		return $ret['up_avg'];
	}
	
	public function lastDownAvgForIp($ip){
		$ret = Doctrine_Query::create()
			->from('TrafficHistory')
			->orderBy('end_time DESC')
			->where('ip = ?', $ip)
			->limit(1)
			->fetchOne();
		return $ret['down_avg'];
	}
	
	public function lastDownForIp($ip){
		$ret = Doctrine_Query::create()
			->from('TrafficHistory')
			->orderBy('end_time DESC')
			->where('ip = ?', $ip)
			->limit(1)
			->fetchOne();
		return $ret['down'];
	}
	
	public static function data_point(){
		date_default_timezone_set('America/New_York');
		// First, get list of ip addresses
		$hosts = Doctrine::getTable('Host')->findAll();
		$ips = array();
		foreach($hosts as $host):
			$ips[$host->ip] = array();
		endforeach;
		// Next, pull out the data for each ip
		foreach($ips as $ip => $stuff):
			$ip_ups = Doctrine_Query::create()
				->select('SUM(bytes) as bytes')
				->from('Traffic')
				->where('srcip = ?', $ip)
				->execute();
			$ip_downs = Doctrine_Query::create()
				->select('SUM(bytes) as bytes')
				->from('Traffic')
				->where('dstip = ?', $ip)
				->execute();
			$ips[$ip]['up'] = $ip_ups[0]['bytes'];
			$ips[$ip]['down'] = $ip_downs[0]['bytes'];
			
		endforeach;
		// Find the last data_point date
		$q = Doctrine_Query::create()
			->from('TrafficHistory')
			->orderBy('end_time DESC')
			->limit(1);
		$date = $q->fetchOne();
		// Date backup - if no data points already exist	
		if(!$date):
			$other = Doctrine_Query::create()
				->from('Traffic')
				->orderBy('date ASC, time ASC')
				->limit(1)
				->fetchOne();
			$started = strtotime($other->date . ' ' . $other->time);
		else:
			$started = strtotime($date->end_time);
		endif;
		$ended = time();
		
		// Wipe the traffic table
		Doctrine_Query::create()
			->delete('Traffic')
			->execute();
		$ret = array();
		// Finally, save data points
		foreach($ips as $ip => $stuff):
			$traff = new TrafficHistory();
			$traff->ip = $ip;
			$traff->up = $stuff['up'];
			$traff->down = $stuff['down'];
			$traff->start_time = date('Y-m-d H:i:s', $started);
			$traff->end_time = date('Y-m-d H:i:s', $ended);
			$tot = $ended - $started;
			$traff->up_avg = $traff->up / $tot;
			$traff->down_avg = $traff->down / $tot;
			$traff->save();
			$ret[$traff->ip] = $traff->toArray(); 
		endforeach;
		return array('message' => 'data saved', 'history' => $ret);
	}
	
}