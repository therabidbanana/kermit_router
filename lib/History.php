<?php


class History extends Kermit_Module{
	public function whenReady($module){
		if($module == 'xmlrpc'):
			$this->xmlrpc->add('history.list', get_class($this), 'history_list');
			$this->xmlrpc->add('history.data_point', get_class($this), 'data_point');
		endif;
	}
	
	public static function history_list(){
		$list = Doctrine::getTable('TotalTraffic')->findAll();
		$ret = array();
		
		foreach($list as $hist):
			$time = strtotime($hist->date ." ".$hist->max) - strtotime($hist->date ." ".$hist->min);
			$avg = $hist->bytes / $time;
			$me = $hist->toArray();
			$me['avg'] = $avg;
			$ret[] = $me;
		endforeach;
		return array('hosts' => $ret);
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
		endforeach;
		return array('message' => 'data saved');
	}
}