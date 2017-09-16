<?php

namespace xavoc\ispmanager;

class Model_UserPlanAndTopup extends \xepan\base\Model_Table{
	public $table = "isp_user_plan_and_topup";
	public $acl_type="ispmanager_user_plan_and_topup";

	function init(){
		parent::init();

		$this->hasOne('xavoc\ispmanager\User','user_id');
		$this->hasOne('xavoc\ispmanager\Plan','plan_id');
		$this->hasOne('xavoc\ispmanager\Condition','condition_id');

		$this->addField('remark');
		$this->addField('is_topup')->type('boolean')->defaultValue(0)->caption('TopUp');

		$this->addField('data_limit'); // no default value keep null as null
		$this->addField('carry_data')->defaultValue(0);
		$this->addExpression('net_data_limit')->set('data_limit+carry_data');
		
		$this->addField('download_limit')->hint('in KBps');
		$this->addField('upload_limit')->hint('in KBps');
		$this->addField('fup_download_limit')->hint('in KBps')->caption('FUP DL');
		$this->addField('fup_upload_limit')->hint('in KBps')->caption('FUP UL');
		$this->addField('accounting_download_ratio')->hint('in %')->caption('ACC DL %');
		$this->addField('accounting_upload_ratio')->hint('in %')->caption('ACC UL %');
		$this->addField('start_date')->type('datetime');
		$this->addField('end_date')->type('datetime');
		$this->addField('expire_date')->type('datetime');
		$this->addField('is_expired')->type('boolean')->defaultValue(false);
		$this->addField('is_recurring')->type('boolean')->defaultValue(false);
		$this->addField('is_effective')->type('boolean')->defaultValue(false);
		$this->addField('download_data_consumed')->hint('in MB')->defaultValue(0);
		$this->addField('upload_data_consumed')->hint('in MB')->defaultValue(0);
		$this->addField('time_limit');
		$this->addField('time_consumed')->system(true);

		$this->addExpression('data_consumed')->set('download_data_consumed+upload_data_consumed');
		
		// row in which consumptin data value to be stored
		$this->addField('data_limit_row');
		$this->addField('duplicated_from_record_id');
		$this->addField('is_data_carry_forward')->enum(['none','once','allways']);
		$this->addField('start_time');
		$this->addField('end_time');

		$this->addField('reset_date')->type('datetime');
		$this->addField('data_reset_value')->type('number');
		$this->addField('data_reset_mode')->enum(['hours','days','months','years']);
		
		$this->addField('sun')->type('boolean')->defaultValue(false);
		$this->addField('mon')->type('boolean')->defaultValue(false);
		$this->addField('tue')->type('boolean')->defaultValue(false);
		$this->addField('wed')->type('boolean')->defaultValue(false);
		$this->addField('thu')->type('boolean')->defaultValue(false);
		$this->addField('fri')->type('boolean')->defaultValue(false);
		$this->addField('sat')->type('boolean')->defaultValue(false);

		$this->addField('d01')->type('boolean')->defaultValue(false);
		$this->addField('d02')->type('boolean')->defaultValue(false);
		$this->addField('d03')->type('boolean')->defaultValue(false);
		$this->addField('d04')->type('boolean')->defaultValue(false);
		$this->addField('d05')->type('boolean')->defaultValue(false);
		$this->addField('d06')->type('boolean')->defaultValue(false);
		$this->addField('d07')->type('boolean')->defaultValue(false);
		$this->addField('d08')->type('boolean')->defaultValue(false);
		$this->addField('d09')->type('boolean')->defaultValue(false);
		$this->addField('d10')->type('boolean')->defaultValue(false);
		$this->addField('d11')->type('boolean')->defaultValue(false);
		$this->addField('d12')->type('boolean')->defaultValue(false);
		$this->addField('d13')->type('boolean')->defaultValue(false);
		$this->addField('d14')->type('boolean')->defaultValue(false);
		$this->addField('d15')->type('boolean')->defaultValue(false);
		$this->addField('d16')->type('boolean')->defaultValue(false);
		$this->addField('d17')->type('boolean')->defaultValue(false);
		$this->addField('d18')->type('boolean')->defaultValue(false);
		$this->addField('d19')->type('boolean')->defaultValue(false);
		$this->addField('d20')->type('boolean')->defaultValue(false);
		$this->addField('d21')->type('boolean')->defaultValue(false);
		$this->addField('d22')->type('boolean')->defaultValue(false);
		$this->addField('d23')->type('boolean')->defaultValue(false);
		$this->addField('d24')->type('boolean')->defaultValue(false);
		$this->addField('d25')->type('boolean')->defaultValue(false);
		$this->addField('d26')->type('boolean')->defaultValue(false);
		$this->addField('d27')->type('boolean')->defaultValue(false);
		$this->addField('d28')->type('boolean')->defaultValue(false);
		$this->addField('d29')->type('boolean')->defaultValue(false);
		$this->addField('d30')->type('boolean')->defaultValue(false);
		$this->addField('d31')->type('boolean')->defaultValue(false);
		$this->addField('treat_fup_as_dl_for_last_limit_row')->type('boolean')->defaultValue(false);
		$this->addField('is_pro_data_affected')->type('boolean')->defaultValue(false);

		$this->addField('burst_dl_limit')->hint('limit per second');
		$this->addField('burst_ul_limit')->hint('limit per second');
		$this->addField('burst_threshold_dl_limit')->hint('limit per second');
		$this->addField('burst_threshold_ul_limit')->hint('limit per second');
		$this->addField('burst_dl_time')->hint('time in second');
		$this->addField('burst_ul_time')->hint('time in second');
		$this->addField('priority');

		$this->addHook('beforeSave',$this);

		$this->add('xavoc\ispmanager\Controller_HumanByte')
			->handleFields([
					'data_limit',
					'download_limit',
					'upload_limit',
					'fup_download_limit',
					'fup_upload_limit',
					'data_consumed'
				]);

		$this->setOrder('id');
		$this->is(['plan_id|required']);
		// $this->add('dynamic_model/Controller_AutoCreator');
	}

	function beforeSave(){
		if($this['start_time']=='') $this['start_time']=null;
		if($this['end_time']=='') $this['end_time']=null;
		if(!$this['data_reset_value']) $this['data_reset_value']=null;
		
	}

	function cron($now=null){
		if(!$now) $now= $this->app->now;

		
	}
}