<?php

/**
 * BaseTraffic
 * 
 * This class has not been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $ID
 * @property string $zeit
 * @property string $datum
 * @property string $name
 * @property integer $uloktets
 * @property integer $dloktets
 * @property string $porttraffic
 * @property string $mac
 * @property string $ip
 * @property string $device
 * @property integer $oktets
 * @property string $srcip
 * @property string $dstip
 * @property string $srcport
 * @property string $dstport
 * @property string $min
 * @property string $max
 * @property string $bytes
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5845 2009-06-09 07:36:57Z jwage $
 */
class TrafficHistory extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('traffic_history');
        $this->hasColumn('start_time', 'timestamp', 25, array(
             'type' => 'timestamp',
             'length' => 25
             ));
        $this->hasColumn('end_time', 'timestamp', 25, array(
             'type' => 'timestamp',
             'length' => 25
             ));
		
		$this->hasColumn('up_avg', 'integer', 10, array(
             'type' => 'integer',
             'length' => 10
             ));
		$this->hasColumn('down_avg', 'integer', 10, array(
             'type' => 'integer',
             'length' => 10
             ));
		$this->hasColumn('up', 'integer', 10, array(
             'type' => 'integer',
             'length' => 10
             ));
		$this->hasColumn('down', 'integer', 10, array(
             'type' => 'integer',
             'length' => 10
             ));
        $this->hasColumn('ip', 'string', 15, array(
             'type' => 'string',
             'length' => 15,
             'fixed' => false,
             'primary' => false,
             'default' => '',
             'notnull' => true,
             'autoincrement' => false,
             ));
    }

}