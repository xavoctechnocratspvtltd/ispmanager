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
		$this->setPlan($this['plan_id']);
		
	}

	function setPlan($plan,$on_date=null,$remove_old=false){

		if(!$on_date) $on_date = $this->app->today;

		if(is_string($plan)) 
			$plan_model = $this->add('xavoc\ispmanager\Model_Plan')->loadBy('name',$plan);
		elseif(is_numeric($plan)) 
			$plan_model = $this->add('xavoc\ispmanager\Model_Plan')->load($plan);
		else
			$plan_model = $plan;

		$condition_model = $this->add('xavoc\ispmanager\Model_Condition')->addCondition('plan_id',$plan_model->id);
		
		// set all plan to expire
		if($remove_old)
			$update_query = "DELETE FROM  isp_user_plan_and_topup WHERE user_id = '".$this->id."' AND is_topup = '0'";
		else
			$update_query = "UPDATE isp_user_plan_and_topup SET is_expired = '1' WHERE user_id = '".$this->id."' AND is_topup = '0'";
		
		$this->app->db->dsql()->expr($update_query)->execute();

		foreach ($condition_model as $key => $condition) {
			$fields = $condition->getActualFields();
			$unset_field =  ['id','plan_id','plan'];
			$fields = array_diff($fields,$unset_field);

			$u_p = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
			$u_p->addCondition('user_id',$this->id)
				->addCondition('is_topup',false);
			$u_p['plan_id'] = $plan_model->id;
			$u_p['is_topup'] = $plan_model['is_topup'];

			// all fields same as condition are setted
			foreach ($fields as $key => $field_name) {
				$u_p[$field_name] = $condition[$field_name];
			}

			$end_date = date("Y-m-d H:i:s", strtotime("+".$plan_model['period']." ".$plan_model['period_unit'],strtotime($on_date)));

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
			$u_p['start_date'] = $on_date;						
			$u_p['end_date'] = $end_date;
			$u_p['expire_date'] = $u_p['is_topup']? $end_date : date("Y-m-d H:i:s", strtotime("'+".$this['grace_period_in_days']." days'",strtotime($end_date)));
			$u_p['is_recurring'] = $plan_model['is_auto_renew'];
			$u_p['reset_date'] = $reset_date;
			$u_p['is_effective'] = 0;
			$u_p['data_limit_row'] = null; //id condition has data_limit then set empty else previous data row limit id;
			$u_p->save();
		}
	}

	function getApplicableRow($now=null,$with_data_limit=false){
		if(!$now) $now = $this->app->now;
		
		$day = strtolower(date("D", strtotime($now)));
		$date = 'd'.strtolower(date("d", strtotime($now)));
		$current_time = date("H:i:s",strtotime($now));
		$today = date('Y-m-d',strtotime($now));

		// if start_time is not null then is me in between start-end time
		// is me (day) checked
		// is me (date) checked
		// not expired
		// order by topup, id desc
		// limit 1

		$user_plans = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
		$q=$user_plans->dsql();
		$q->where($q->expr("
				(
					(
						(
							CAST('$current_time' AS time) BETWEEN `start_time` AND `end_time` 
							OR 
							(
								NOT CAST('$current_time' AS time) BETWEEN `end_time` AND `start_time` 
								AND `start_time` > `end_time`
							)
						) 
						AND
						is_expired=0
					)
					OR
					(
						`start_time` is null
					)
				)
				AND
				`$day`=1
				AND
				`$date` = 1
				AND
				(
					'$now' >= start_date
					AND
					'$now' <= end_date
				)
				".
				($with_data_limit? " AND data_limit isnot null AND data_limit >0 ":'')
				)
		);

		$q->order('is_topup desc, id desc');
		$q->limit(1);

		$x = $q->debug()->getHash();
		return $x;
	}

	function getAAADetails($now=null,$accounting_data=null){
		if(!$now) $now = $this->app->now;

		// if accounting data
			// add in effective_row=1
		// run effectiveDataRecord again to set flag in database
		// run getDlUl

		$applicable_row = $this->getApplicableRow($now);

		$data_limit_row = $applicable_row;

		if(!$applicable_row['data_limit']) $data_limit_row = $this->getApplicableRow($now,$with_data_limit=true);
		
		// bandwidth or fup ??
		$if_fup='fup_';
		if(($data_limit_row['download_data_consumed'] + $data_limit_row['upload_data_consumed']) < $data_limit_row['data_limit']){
			$if_fup='';
		}
		$dl_field = $if_fup.'download_limit';
		$ul_field = $if_fup.'upload_limit';

		// but from which row ??
		// from applicable if values exists
		$dl_limit = $applicable_row[$dl_field];
		$ul_limit = $applicable_row[$ul_field];

		if(!$dl_limit) $dl_limit = $data_limit_row[$dl_field];
		if(!$ul_limit) $ul_limit = $data_limit_row[$ul_field];
		// from data if not 
		// if fup is null or 0 it is a reject authentication command 

		$access= true;
		if(!$dl_limit && !$ul_limit) $access=false;
		$final_row = $applicable_row;
		$final_row['dl_limit'] = $dl_limit;
		$final_row['ul_limit'] = $ul_limit;
		$final_row['data_limit'] = $data_limit_row['data_limit'];
		$final_row['download_data_consumed'] = $data_limit_row['download_data_consumed'];
		$final_row['upload_data_consumed'] = $data_limit_row['upload_data_consumed'];

		return ['access'=>$access, 'result'=>$final_row];
	}

	function setEffectiveDataRecord($now=null){
		if(!$now) $now = $this->app->now;

	}

}