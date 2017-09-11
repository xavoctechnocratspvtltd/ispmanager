<?php

namespace xavoc\ispmanager;

class Model_Device extends \xepan\base\Model_Table{ 
	public $table = "isp_devices";
	public $acl = false;
	function init(){
		parent::init();

		$this->hasOne('xavoc\ispmanager\Device','parent_id');
		// $this->addField('parent_id')->type('int');
		
		$this->addField('name');
		$this->addField('ip');
		$this->addField('port');
		$this->addField('status')->enum(['Active']);
		$this->addField('monitor')->enum(['ping','host-port']);
		$this->addField('failed_action')->type('text')->system(true)->defaultValue("exec 'wget http://{xepan_host}/?page=xepan_ispmanager_devicedown&failed_device_id={device_id}'");
		$this->addField('allowed_fail_cycle')->type('int');

		// $this->add('dynamic_model/Controller_AutoCreator');
	}

}