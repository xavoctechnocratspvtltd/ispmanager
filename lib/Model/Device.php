<?php

namespace xavoc\ispmanager;

class Model_Device extends \xepan\base\Model_Table{ 
	public $table = "isp_devices";

	function init(){
		parent::init();

		// $this->hasOne('xavoc\ispmanager\Device','parent_id');
		$this->addField('parent_id')->type('int');
		
		$this->addField('name');
		$this->addField('ip');
		$this->addField('port');
		$this->addField('status')->enum(['Active']);
		$this->addField('monitor')->enum(['ping','host-port']);
		$this->addField('failed_action')->type('text');
		$this->addField('allowed_fail_cycle')->type('int');

		$this->add('dynamic_model/Controller_AutoCreator');
	}

}