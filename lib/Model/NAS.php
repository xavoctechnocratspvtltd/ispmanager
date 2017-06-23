<?php

namespace xavoc\ispmanager;

class Model_NAS extends \xepan\base\Model_Table{ 
	public $table = "isp_nas";
	
	public $acl_type="ispmanager_nas";
	function init(){
		parent::init();

		$this->addField('name');
		$this->addField('address');
		$this->addField('city');
		$this->addField('state');
		$this->addField('country');
		
		$this->add('dynamic_model/Controller_AutoCreator');
	}
}