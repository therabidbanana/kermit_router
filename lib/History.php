<?php


class History extends Kermit_Module{
	public function whenReady($module){
		if($module == 'xmlrpc'):
			$this->xmlrpc->add('history.list', get_class($this), 'history_list');
			$this->xmlrpc->add('history.last_ten', get_class($this), 'lastTen');
			$this->xmlrpc->add('history.last_hour', get_class($this), 'lastHour');
			$this->xmlrpc->add('history.last_day', get_class($this), 'lastDay');
			$this->xmlrpc->add('history.data_point', get_class($this), 'dataPoint');
			$this->xmlrpc->add('history.lastTen', get_class($this), 'lastTen');
			$this->xmlrpc->add('history.lastHour', get_class($this), 'lastHour');
			$this->xmlrpc->add('history.lastDay', get_class($this), 'lastDay');
			$this->xmlrpc->add('history.dataPoint', get_class($this), 'dataPoint');
			$this->xmlrpc->add('history.lastDataPoint', get_class($this), 'lastDataPoint');
		endif;
	}
	
	public static function history_list(){
		global $kermit;
		$hosts = Doctrine::getTable('Host')->findAll();
		$ret = array();
		foreach($hosts as $host):
			//$history = Doctrine_Query::create()
			//	->from('TrafficHistory')
			//	->where('ip = ?', $host->ip)
			//	->orderBy('end_time ASC')
			//	->execute();
			//$ret[$host->ip] = $history->toArray();
		endforeach;
		$ret = array('history' => $ret);
		// $kermit->xmlrpc->log('history.list', 'Listing history', array());
		return $ret;
	}
	
	public static function lastTen(){
		global $kermit;
		$hosts = Doctrine::getTable('Host')->findAll();
		$ret = array();
		foreach($hosts as $host):
			$ret[] = $kermit->history->historyBlocksForIp($host->ip, time() - 10*60, 5);
		endforeach;
		$kermit->xmlrpc->log('history.lastTen', 'Listing history for last ten minutes', array('history' => $ret));
		return array('history' => $ret);
	}
	

	
	public static function lastHour(){
		global $kermit;
		$hosts = Doctrine::getTable('Host')->findAll();
		$ret = array();
		foreach($hosts as $host):
			$ret[] = $kermit->history->historyBlocksForIp($host->ip, time() - 60*60, 12, 5*60);
		endforeach;
		
		$kermit->xmlrpc->log('history.lastHour', 'Listing history for last hour', array('history' => $ret));
		return array('history' => $ret);
	}
	
	public static function lastDay(){
		global $kermit;
		$hosts = Doctrine::getTable('Host')->findAll();
		$ret = array();
		foreach($hosts as $host):
			$ret[] = $kermit->history->historyBlocksForIp($host->ip, time() - 24*60*60, 24, 60*60);
		endforeach;
		$kermit->xmlrpc->log('history.lastDay', 'Listing history for last day', array('history' => $ret));
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
			->select('	ip, MIN(start_time) as start_time, MAX(end_time) as end_time, 
						SUM(up) as up, SUM(down) as down, AVG(up_avg) as up_avg, AVG(down_avg) as down_avg')
			->from('TrafficHistory')
			->where('ip = ?', $ip)
			->andWhere('start_time > ? AND start_time < ?', array($date1, $date2))
			->andWhere('end_time < ? AND end_time > ?', array($date2, $date1))
			->orderBy('end_time ASC');
		
		$ret = $history->fetchOne();
		if($ret) return $ret->toArray();
		return $ret;
	}
	
	public function historyBlocksForIp($ip, $start_time, $block_count = 12, $block_size = 120){
		$blocks = array();
		for($i = 0; $i < $block_count; $i++){
			$start = $start_time + ($i*$block_size);
			$end = $start + $block_size;
			$block = $this->dateRangeForIp($ip, $start, $end);
			if(!$block || !isset($block['up'])){
				$block['up'] = 0;
				$block['down'] =0;
				$block['up_avg'] = 0;
				$block['down_avg'] = 0;
			}
			else{
			$kerm = $this->who->kermitForIp($ip);
			$block['hostname'] = $kerm['name'];
			$block['up'] = $block['up'] / (1024 * 1024);
			$block['down'] = $block['down'] / (1024 * 1024);
			$block['up_avg'] = $block['up_avg'] / (1024 * 1024);
			$block['down_avg'] = $block['down_avg'] / (1024 * 1024);
			}
			if($block && !empty($block->ip)) $blocks[] = $block;
		}
		return $blocks;
	}
	
	public function lastStatsForIp($ip){
		$ret = Doctrine_Query::create()
			->select('up, up_avg, down, down_avg')
			->from('TrafficHistory')
			->orderBy('end_time DESC')
			->where('ip = ?', $ip)
			->limit(1)
			->setHydrationMode(Doctrine::HYDRATE_ARRAY)
			->fetchOne();
		return $ret;
	}
	
	public function lastUpForIp($ip){
		$ret = Doctrine_Query::create()
			->from('TrafficHistory')
			->orderBy('end_time DESC')
			->where('ip = ?', $ip)
			->limit(1)
			->setHydrationMode(Doctrine::HYDRATE_ARRAY)
			->fetchOne();
		return $ret['up'];
	}
	public function lastUpAvgForIp($ip){
		$ret = Doctrine_Query::create()
			->from('TrafficHistory')
			->orderBy('end_time DESC')
			->where('ip = ?', $ip)
			->limit(1)
			->setHydrationMode(Doctrine::HYDRATE_ARRAY)
			->fetchOne();
		return $ret['up_avg'];
	}
	
	public function lastDownAvgForIp($ip){
		$ret = Doctrine_Query::create()
			->from('TrafficHistory')
			->orderBy('end_time DESC')
			->where('ip = ?', $ip)
			->limit(1)
			->setHydrationMode(Doctrine::HYDRATE_ARRAY)
			->fetchOne();
		return $ret['down_avg'];
	}
	
	public function lastDownForIp($ip){
		$ret = Doctrine_Query::create()
			->from('TrafficHistory')
			->orderBy('end_time DESC')
			->where('ip = ?', $ip)
			->limit(1)
			->setHydrationMode(Doctrine::HYDRATE_ARRAY)
			->fetchOne();
		return $ret['down'];
	}
	
	public static function dataPoint($log = true){
		global $kermit;
		date_default_timezone_set('America/New_York');
		// First, get list of ip addresses
		$hosts = Doctrine::getTable('Host')->findAll();
		$ips = array();
		foreach($hosts as $host):
			$ips[$host->ip] = array();
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
		
		$cut_date = date('Y-m-d', $started);
		$cut_time = date('H:i:s', $started);
		// Next, pull out the data for each ip
		foreach($ips as $ip => $stuff):
			$ip_ups = Doctrine_Query::create()
				->select('SUM(bytes) as bytes')
				->from('Traffic')
				->where('srcip = ?', $ip)
				->andWhere('(date > ?) OR (date = ? AND time > ?)', array($cut_date, $cut_date, $cut_time))
				->execute();
			$ip_downs = Doctrine_Query::create()
				->select('SUM(bytes) as bytes')
				->from('Traffic')
				->where('dstip = ?', $ip)
				->andWhere('(date > ?) OR (date = ? AND time > ?)', array($cut_date, $cut_date, $cut_time))
				->execute();
			$ips[$ip]['up'] = $ip_ups[0]['bytes'];
			$ips[$ip]['down'] = $ip_downs[0]['bytes'];
			
		endforeach;
		
		$ended = time();
		
		$del_date = date('Y-m-d', ($started - (60*60*24*40))); // Delete history older than 40 days
		// Wipe the traffic table
		Doctrine_Query::create()
			->delete('Traffic')
			->where('date < ?', $del_date)
			->execute();
		
		// Delete traffic history older than new data point by 30 days
		Doctrine_Query::create()
			->delete('TrafficHistory')
			->where('end_time < ?', date('Y-m-d H:i:s', ($ended - 60*60*24*30)))
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
		
		if($log) $kermit->xmlrpc->log('history.dataPoint', 'Creating a new data point', array('message' => 'data saved', 'history' => $ret));
		return array('message' => 'data saved', 'history' => $ret);
	}
	
	public static function lastDataPoint(){
		global $kermit;
		date_default_timezone_set('America/New_York');
		$x = Doctrine_Query::create()
			->from('TrafficHistory')
			->orderBy('end_time DESC')			
			->fetchOne();
		if(!$x) return array('message' => 'No data points available', 'error' => 1);
		$y = strtotime($x->end_time);
		$z = time();
		if($z - $y > 90) $ret = array('message' => 'Data point is too old by '.($z - $y - 60).' seconds', 'error' => 1);
		else $ret = array('message' => 'Data point taken at valid time '.$x->end_time, 'error' => 0);
		$kermit->xmlrpc->log('history.lastDataPoint', 'Checking data point', $ret);
		return $ret;
	}
}
