<?php

namespace xavoc\ispmanager;

class Model_Chanel extends \xepan\base\Model_Table{ 
	public $table = "isp_chanel";
	public $acl_type="ispmanager_chanel";
	
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