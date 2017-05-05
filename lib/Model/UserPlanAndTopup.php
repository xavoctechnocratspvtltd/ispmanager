<?php

namespace xavoc\ispmanager;

class Model_UserPlanAndTopup extends \xepan\base\Model_Table{
	public $table = "isp_user_plan_and_topup";
	public $acl_type="ispmanager_user_plan_and_topup";

	function init(){
		parent::init();

		$this->hasOne('xavoc\ispmanager\User','user_id');
		$this->hasOne('xavoc\ispmanager\BasicPlan','plan_id');

		$this->addField('is_topup')->type('boolean')->defaultValue(0);

		$this->addField('data_limit');
		$this->addField('download_limit');
		$this->addField('upload_limit');
		$this->addField('fup_download_limit');
		$this->addField('fup_upload_limit');
		$this->addField('accounting_download_ratio');
		$this->addField('accounting_upload_ratio');
		$this->addField('start_date')->type('datetime');
		$this->addField('end_date')->type('datetime');
		$this->addField('expire_date')->type('datetime');
		$this->addField('is_expired')->type('boolean')->defaultValue(false);
		$this->addField('is_recurring')->type('boolean')->defaultValue(false);
		$this->addField('is_effective')->type('boolean')->defaultValue(false);
		$this->addField('download_data_consumed');
		$this->addField('upload_data_consumed');
		$this->addField('data_limit_row');
		$this->addField('duplicated_from_record_id');
		$this->addField('is_data_carry_forward')->type('boolean')->defaultValue(false);
		$this->addField('start_time');
		$this->addField('end_time');
		
		$this->addField('sun')->type('boolean')->defaultValue(false);
		$this->addField('mon')->type('boolean')->defaultValue(false);
		$this->addField('tue')->type('boolean')->defaultValue(false);
		$this->addField('wed')->type('boolean')->defaultValue(false);
		$this->addField('thu')->type('boolean')->defaultValue(false);
		$this->addField('fri')->type('boolean')->defaultValue(false);
		$this->addField('sat')->type('boolean')->defaultValue(false);

		$this->addField('d1')->type('boolean')->defaultValue(false);
		$this->addField('d2')->type('boolean')->defaultValue(false);
		$this->addField('d3')->type('boolean')->defaultValue(false);
		$this->addField('d4')->type('boolean')->defaultValue(false);
		$this->addField('d5')->type('boolean')->defaultValue(false);
		$this->addField('d6')->type('boolean')->defaultValue(false);
		$this->addField('d7')->type('boolean')->defaultValue(false);
		$this->addField('d8')->type('boolean')->defaultValue(false);
		$this->addField('d9')->type('boolean')->defaultValue(false);
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

		$this->addField('reset_date')->type('datetime');
		$this->addField('data_reset_mode')->enum(['hourly','daily','monthly','yearly']);

		$this->add('dynamic_model/Controller_AutoCreator');
	}
}