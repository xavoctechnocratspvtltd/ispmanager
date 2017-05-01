<?php

namespace xavoc\ispmanager;

class Model_State extends \xepan\base\Model_Table{ 
	public $table = "isp_state";
	
	public $acl_type="ispmanager_state";
	function init(){
		parent::init();

		$this->hasOne('xavoc\ispmanager\Country','country_id');
		$this->addField('name');
		
		$this->hasMany('xavoc\ispmanager\City','city_id');

		$this->add('dynamic_model/Controller_AutoCreator');
	}
}