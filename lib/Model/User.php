<?php

namespace xavoc\ispmanager;

class Model_User extends \xepan\base\Model_Table{ 
	public $table = "isp_user";
	public $status = ['active','deactive'];
	public $actions = [
				'active'=>['view','edit','delete','plans'],
				'deactive'=>['view','edit','delete','active']
				];
	public $acl_type= "ispmanager_user";
	private $plan_dirty = false;
	function init(){
		parent::init();

		$this->hasOne('xavoc\ispmanager\Plan','plan_id');

		$this->addField('name');
		$this->addField('password');
		$this->addField('mac_address_cm')->caption('MAC Address CM');
		$this->addField('ip_address_mode_cm')->setValueList(['ip pool'=>'ip pool','static ip'=>'static ip']);
		$this->addField('cm_ip_pool');
		$this->addField('cm_static_ip');
		
		$this->addField('mac_address_cpe')->caption('MAC Address CPE');
		$this->addField('allow_mac_address_cpe_only')->type('boolean');
		$this->addField('ip_address_mode_cpe')->setValueList(['nas pool or dhcp'=>'NAS Pool or DHCP','ip pool'=>'IP pool','static ip'=>'static IP']);
		
		$this->addField('simultaneous_use')->type('Number');
		$this->addField('first_name');
		$this->addField('last_name');
		$this->addField('company_name');
		$this->addField('address');
		$this->addField('city');
		$this->addField('zip');
		$this->addField('country');
		$this->addField('state');
		$this->addField('contact_number');
		$this->addField('email_id');
		$this->addField('vat_id');
		$this->addField('narration')->type('text');
		$this->addField('grace_period_in_days')->type('number')->defaultValue(0);
		$this->addField('custom_radius_attributes')->type('text')->caption('Custom RADIUS Attributes');

		$this->addField('is_verified')->type('boolean');
		$this->addField('verified_by')->enum(['email','otp','social']);
		$this->addField('status')->enum(['active','deactive'])->defaultValue('active');
		$this->addField('created_at')->type('datetime')->defaultValue($this->app->now);
		$this->hasMany('xavoc\ispmanager\TopUp','topup_id',null,'topups');

		$this->add('dynamic_model/Controller_AutoCreator');

		$this->is([
				'name|to_trim|required',
				'password|number|>=4',
				'created_at|to_trim|required'
			]);

		$this->addHook('beforeSave',$this);
		$this->addHook('afterSave',[$this,'updateUserConditon']);
	}

	function beforeSave(){
		if($this->dirty['plan_id']){
			$this->plan_dirty = true;
		}
	}

	function updateUserConditon(){
		if(!$this->plan_dirty) return;

		$plan_model = $this->add('xavoc\ispmanager\Model_Plan')->load($this['plan_id']);
		$condition_model = $this->add('xavoc\ispmanager\Model_Condition')->addCondition('plan_id',$plan_model->id);
		
		// set all plan to expire
		$update_query = "UPDATE isp_user_plan_and_topup SET is_expired = '1' WHERE user_id = '".$this->id."' AND is_topup = '0'";
		$this->app->db->dsql()->expr($update_query)->execute();

		foreach ($condition_model as $key => $condition) {
			$fields = $condition->getActualFields();
			$unset_field =  ['id','plan_id','plan'];
			$fields = array_diff($fields,$unset_field);

			$u_p = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
			$u_p->addCondition('user_id',$this->id)
				->addCondition('is_topup',false);
			$u_p['plan_id'] = $this['plan_id'];
			$u_p['is_topup'] = $plan_model['is_topup'];

			// all fields same as condition are setted
			foreach ($fields as $key => $field_name) {
				$u_p[$field_name] = $condition[$field_name];
			}

			$end_date = date("Y-m-d H:i:s", strtotime("'+".$plan_model['period']." ".$plan_model['period_unit']."'",strtotime($this->app->now)));
			$reset_date = date("Y-m-d H:i:s", strtotime("'+".$condition['data_reset_value']." ".$condition['data_reset_mode']."'",strtotime($this->app->now)));
			if($condition['data_reset_mode'] == "months"){
				$reset_date = date('Y-m-01 00:00:00', strtotime($reset_date));
			}elseif ($condition['data_reset_mode'] == "years") {
				$reset_date = date('Y-m-01 00:00:00', strtotime($reset_date));
			}elseif ($condition['data_reset_mode'] == "days") {
				$reset_date = date('Y-m-d 00:00:00', strtotime($reset_date));
			}elseif($condition['data_reset_mode'] == "hours"){
				$reset_date = date('Y-m-d H:00:00', strtotime($reset_date));
			}

			// factor based on implemention
			$u_p['start_date'] = $this->app->now;						
			$u_p['end_date'] = $end_date;
			$u_p['expire_date'] = date("Y-m-d H:i:s", strtotime("'+".$this['grace_period_in_days']." days'",strtotime($end_date)));
			$u_p['is_recurring'] = $plan_model['is_auto_renew'];
			$u_p['reset_date'] = $reset_date;
			$u_p['is_effective'] = 0;
			$u_p['data_limit_row'] = null; //id condition has data_limit then set empty else previous data row limit id;
			$u_p->save();
		}
	}

	function addPlan($plan){

	}

	function getCurrent($now=null,$accounting_data=null){
		if(!$now) $now = $this->app->now;

		// if accounting data
			// add in effective_row=1
		// run effectiveDataRecord again to set flag in database
		// run getDlUl
		return ['access'=>true,'dl'=>512,'ul'=>512];
	}

	function setEffectiveDataRecord($now=null){
		if(!$now) $now = $this->app->now;

	}

}