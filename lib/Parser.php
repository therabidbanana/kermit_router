<?php

class Parser extends Kermit_Module{
	public function afterLoad(){
		$this->core->load('vendor/spyc');
	}
	
	public function yaml($file){
		return spyc_load_file($file);
	}
}