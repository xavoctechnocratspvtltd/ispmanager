<?php

namespace xavoc\ispmanager;

class Model_Condition extends \xepan\base\Model_Table{ 
	public $table = "isp_condition";
	
	public $acl_type="ispmanager_plan";
	function init(){
		parent::init();

		$this->hasOne('xavoc\ispmanager\Policy','policy_id');

		$this->addField('factor')->enum(['daily_uses','monthly_uses','yearly_uses','day','date','time']);
		
		// for factor day
		$this->addField('mon')->type('boolean')->defaultValue(false);
		$this->addField('tue')->type('boolean')->defaultValue(false);
		$this->addField('wed')->type('boolean')->defaultValue(false);
		$this->addField('thu')->type('boolean')->defaultValue(false);
		$this->addField('fri')->type('boolean')->defaultValue(false);
		$this->addField('sat')->type('boolean')->defaultValue(false);
		$this->addField('sun')->type('boolean')->defaultValue(false);

		// for factor date
		$this->addField('start_date')->type('datetime')->defaultValue($this->app->now);
		$this->addField('end_date')->type('datetime')->defaultValue($this->app->now);
		
		// for factor time
		$this->addField('starting_time')->type('time');
		$this->addField('ending_time')->type('time');

		$this->addField('operator')->enum(['>','<','>=','<=']);
		$this->addField('value')->hint('Data Consumption in GB');

		$this->add('dynamic_model/Controller_AutoCreator');
	}
}