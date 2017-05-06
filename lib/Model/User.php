<?php

namespace xavoc\ispmanager;

class Model_User extends \xepan\commerce\Model_Customer{ 
	// public $table = "isp_user";
	public $status = ['Active','InActive'];
	public $actions = [
				'Active'=>['view','edit','delete','plans'],
				'InActive'=>['view','edit','delete','active']
				];
	public $acl_type= "ispmanager_user";
	private $plan_dirty = false;

	function init(){
		parent::init();

		// destroy extra fields
		// $cust_fields = $this->add('xepan\commerce\Model_Customer')->getActualFields();
		$destroy_field = ['assign_to_id','scope','user_id','is_designer','score','freelancer_type','related_with','related_id','assign_to','billing_country_id','billing_state_id','shipping_country_id','shipping_state_id','billing_name','billing_address','billing_city','billing_pincode','same_as_billing_address','shipping_name','shipping_address','shipping_city','shipping_pincode','created_by_id','source'];
		foreach ($destroy_field as $key => $field) {
			if($this->hasElement($field))
				$this->getElement($field)->destroy();
		}

		$user_j = $this->join('isp_user.customer_id');

		$user_j->hasOne('xavoc\ispmanager\Plan','plan_id');

		$user_j->addField('radius_username')->caption('Username');
		$user_j->addField('radius_password')->caption('Password');
		$user_j->addField('simultaneous_use')->type('Number');
		$user_j->addField('grace_period_in_days')->type('number')->defaultValue(0);
		$user_j->addField('custom_radius_attributes')->type('text')->caption('Custom RADIUS Attributes');
		$user_j->addField('create_invoice')->type('boolean')->defaultValue(false);
		$user_j->addField('include_pro_data_basis')->type('boolean')->defaultValue(false);

		$user_j->hasMany('xavoc\ispmanager\TopUp','topup_id',null,'topups');

		// $this->add('dynamic_model/Controller_AutoCreator');
		$this->is(['plan_id|to_trim|required']);

		$this->addHook('beforeSave',$this);
		$this->addHook('afterSave',[$this,'updateUserConditon','createInvoice']);
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

	function createInvoice(){
		if(!$this->plan_dirty) return;
		
	}

	function setPlan($plan, $on_date=null, $remove_old=false){

		if(!$on_date) $on_date = isset($this->app->isptoday)? $this->app->isptoday : $this->app->today;

		if(is_numeric($plan))
			$plan_model = $this->add('xavoc\ispmanager\Model_Plan')->load($plan);
		elseif(is_string($plan))
			$plan_model = $this->add('xavoc\ispmanager\Model_Plan')->loadBy('name',$plan);
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
		if(!$now) $now = isset($this->app->ispnow)? $this->app->ispnow : $this->app->now;
		
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
		if(!$now) $now = isset($this->app->ispnow)? $this->app->ispnow : $this->app->now;

		// if accounting data
			// add in effective_row=1
		// run effectiveDataRecord again to set flag in database
		// run getDlUl

		$applicable_row = $this->getApplicableRow($now);

		$data_limit_row = $applicable_row;

		if(!$applicable_row['data_limit']) $data_limit_row = $this->getApplicableRow($now,$with_data_limit=true);
		
		// bandwidth or fup ??
		$if_fup='fup_';
		if(($data_limit_row['download_data_consumed'] + $data_limit_row['upload_data_consumed']) < $this->app->toMB($data_limit_row['data_limit'])){
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
		if(!$now) $now = isset($this->app->ispnow)? $this->app->ispnow : $this->app->now;

	}

}