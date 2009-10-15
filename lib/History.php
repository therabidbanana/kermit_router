<?php


class History extends Kermit_Module{
	public function whenReady($module){
		if($module == 'xmlrpc'):
			$this->xmlrpc->add('history.list', get_class($this), 'history_list');
		endif;
	}
	
	public static function history_list(){
		$list = Doctrine::getTable('TotalTraffic')->findAll();
		$ret = array();
		foreach($list as $hist):
			
		endforeach;
		return $list->toArray();
	}
	
	
}