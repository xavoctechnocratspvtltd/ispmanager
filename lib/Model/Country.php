<?php

namespace xavoc\ispmanager;

class Model_Country extends \xepan\base\Model_Table{ 
	public $table = "isp_country";
	
	public $acl_type="ispmanager_country";
	function init(){
		parent::init();

		$this->addField('name');

		$this->hasMany('xavoc\ispmanager\State','country_id');
		$this->add('dynamic_model/Controller_AutoCreator');
	}
}