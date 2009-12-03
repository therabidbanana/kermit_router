<?php

/**
 * Log Model
 * 
 */
class SpeedLog extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('speed'); 
        $this->hasColumn('up_mbps', 'float');
		$this->hasColumn('down_mbps', 'float');
        $this->hasColumn('created_at', 'timestamp', 25, array(
             'type' => 'timestamp',
             'length' => 25
             ));
    }

}