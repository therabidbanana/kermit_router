<?php

/**
 * Log Model
 * 
 */
class Log extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('log'); 

        $this->hasColumn('xmlrpc_call as call', 'string',255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'primary' => false,
             'default' => '',
             'notnull' => true,
             'autoincrement' => false,
             ));
		$this->hasColumn('args', 'string',255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'primary' => false,
             'default' => '',
             'notnull' => true,
             'autoincrement' => false,
             ));
		$this->hasColumn('message', 'string',255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'primary' => false,
             'default' => '',
             'notnull' => true,
             'autoincrement' => false,
             ));
			 
		$this->hasColumn('xmlrpc_return as ret', 'string',10000, array(
             'type' => 'string',
             'length' => 10000,
             'fixed' => false,
             'primary' => false,
             'default' => '',
             'notnull' => true,
             'autoincrement' => false,
             ));
    }

}