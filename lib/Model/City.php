<?php

namespace xavoc\ispmanager;

class Model_City extends \xepan\base\Model_Table{ 
	public $table = "isp_city";
	
	public $acl_type="ispmanager_city";
	function init(){
		parent::init();

		$this->hasOne('xavoc\ispmanager\State','state_id');
		$this->addField('name');
		
		$this->add('dynamic_model/Controller_AutoCreator');
	}
}