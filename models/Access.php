<?php

/**
 * Access Control Model
 * 
 */
class Access extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('access'); 
		$this->hasColumn('is_host', 'boolean', 1, array(
             'type' => 'boolean',
             'length' => 1,
             'unsigned' => 0,
             'primary' => false,
             'autoincrement' => false,
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
        $this->hasColumn('service', 'string',255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'primary' => false,
             'default' => '',
             'notnull' => true,
             'autoincrement' => false,
             ));
		$this->hasColumn('level', 'integer', 1, array(
             'type' => 'integer',
             'length' => 1,
             'unsigned' => 0,
             'primary' => false,
             'autoincrement' => false,
             ));
    }

}