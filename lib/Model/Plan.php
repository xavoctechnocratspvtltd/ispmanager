<?php

namespace xavoc\ispmanager;

class Model_Plan extends \xepan\base\Model_Table{ 
	public $table = "isp_plan";
	
	public $acl_type="ispmanager_plan";
	function init(){
		parent::init();

		$this->addField('name');
		$this->addField('description');
		$this->addField('total_limit')->type('number');
		$this->addField('price')->type('number');
		$this->addField('mode')->enum(['monthly','quarterly','half yearly','yearly']);
		$this->addField('type_of_plan')->enum(['prepaid','postpaid']);
		$this->addField('data_rate_dl')->type('number')->hint('DL: Download Limit');
		$this->addField('data_rate_ul')->type('number')->hint('UL: Upload Limit');
		
		$this->addField('after_limit')->enum(['close','capping']);
		$this->addField('after_limit_dl')->type('number')->hint('DL: Download Limit')->defaultValue(0);
		$this->addField('after_limit_ul')->type('number')->hint('UL: Upload Limit')->defaultValue(0);

		$this->addField('available_in_user_control_panel')->type('boolean');
		$this->addField('status')->enum(['active','inactive']);

		$this->hasMany('xavoc\ispmanager\Policy','plan_id');
		$this->add('dynamic_model/Controller_AutoCreator');
	}
}