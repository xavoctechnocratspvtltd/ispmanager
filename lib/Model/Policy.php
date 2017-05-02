<?php

namespace xavoc\ispmanager;

class Model_Policy extends \xepan\base\Model_Table{ 
	public $table = "isp_policy";
	
	public $acl_type="ispmanager_plan";
	function init(){
		parent::init();

		$this->hasOne('xavoc\ispmanager\Plan','plan_id');
		
		$this->addField('data_dl')->type('number')->hint('DL: download limit');
		$this->addField('data_ul')->type('number')->hint('uL: download limit');

		$this->addField('data_accounting_dl')->type('number')->hint('DL: upload limit');
		$this->addField('data_accounting_ul')->type('number')->hint('UL: upload limit');
		$this->addField('reject')->type('boolean')->defaultValue(false);

		$this->hasMany('xavoc\ispmanager\Condition','policy_id');
		$this->add('dynamic_model/Controller_AutoCreator');
	}
}