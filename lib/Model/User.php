<?php

namespace xavoc\ispmanager;

class Model_User extends \xepan\commerce\Model_Customer{ 
	// public $table = "isp_user";
	public $status = ['Active','InActive'];
	public $actions = [
				'Active'=>['view','edit','delete','Topups'],
				'InActive'=>['view','edit','delete','active']
				];
	public $acl_type= "ispmanager_user";
	private $plan_dirty = false;

	function init(){
		parent::init();

		// destroy extra fields
		// $cust_fields = $this->add('xepan\commerce\Model_Customer')->getActualFields();
		$destroy_field = ['assign_to_id','scope','user_id','is_designer','score','freelancer_type','related_with','related_id','assign_to','created_by_id','source'];
		foreach ($destroy_field as $key => $field) {
			if($this->hasElement($field))
				$this->getElement($field)->system(true);
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

		$user_j->hasMany('xavoc\ispmanager\UserPlanAndTopup','user_id');
		// $user_j->hasMany('xavoc\ispmanager\TopUp','user_id',null,'topups');

		// $this->add('dynamic_model/Controller_AutoCreator');
		// $this->is(['plan_id|to_trim|required']);

		// $this->addExpression('plan_data_limit')->set(function($m,$q){
		// 	$m->add('xavoc\ispmanager\Model_UserPlanAndTopup')
		// 		->addCondition('user_id',$m->id)
		// 		->addCondition('is_topup',false)
		// 		->addCondition([['is_expired',0],['is_expired',null]])
		// 		;
		// 	return $m->sum('net_data_limit');
		// });
		// $this->addExpression('consumed_limit');

		$this->addHook('beforeSave',$this);
		$this->addHook('afterSave',[$this,'updateUserConditon']);
		$this->addHook('afterSave',[$this,'createInvoice']);
		$this->addHook('afterSave',[$this,'updateNASCredential']);

		$this->is(
				['radius_username|to_trim|unique']
				['plan_id|to_trim|reuired']
			);
	}

	function beforeSave(){

		if($this->dirty['plan_id']){
			$this->plan_dirty = $this->dirty['plan_id'];
		}
		
		$this['billing_country_id'] = $this['billing_country_id']?:$this['country_id'];
		$this['billing_state_id'] = $this['billing_state_id']?:$this['state_id'];
		$this['billing_city'] = $this['billing_city']?:$this['city'];
		$this['billing_address'] = $this['billing_address']?:$this['address'];
		$this['billing_name'] = $this['billing_name']?:$this['organization_name'];
		$this['billing_pincode'] = $this['billing_pincode']?:$this['pin_code'];
		
		$this['shipping_country_id'] = $this['shipping_country_id']?:$this['country_id'];
		$this['shipping_state_id'] = $this['shipping_state_id']?:$this['state_id'];
		$this['shipping_city'] = $this['shipping_city']?:$this['city'];
		$this['shipping_address'] = $this['shipping_address']?:$this['address'];
		$this['shipping_name'] = $this['shipping_name']?:$this['organization_name'];
		$this['shipping_pincode'] = $this['shipping_pincode']?:$this['pin_code'];

	}

	function updateUserConditon(){
		if(!$this->plan_dirty AND !$this['plan_id']) return;
		$this->setPlan($this['plan_id']);
		
	}

	function createInvoice(){
		if(!$this->plan_dirty AND !$this['create_invoice'] ) return;

		if(!$this->loaded()) throw new \Exception("model radius user must loaded");
		$this->reload();

		$qsp_master = $this->add('xepan\commerce\Model_QSP_Master');
		$master_data = [];

		$master_data['qsp_no'] = $this->add('xepan\commerce\Model_SalesInvoice')->newNumber();
		$master_data['contact_id'] = $this->id;
		$master_data['currency_id'] = $this->app->epan->default_currency->get('id');
		$master_data['billing_country_id'] = $this['billing_country_id'];
		$master_data['billing_state_id'] = $this['billing_state_id'];
		$master_data['billing_city'] = $this['billing_city'];
		$master_data['billing_address'] = $this['billing_address'];
		$master_data['billing_name'] = $this['billing_name'];
		$master_data['billing_pincode'] = $this['billing_pincode'];

		$master_data['shipping_country_id'] = $this['shipping_country_id'];
		$master_data['shipping_state_id'] = $this['shipping_state_id'];
		$master_data['shipping_name'] = $this['shipping_name'];
		$master_data['shipping_address'] = $this['shipping_address'];
		$master_data['shipping_city'] = $this['shipping_city'];
		$master_data['shipping_pincode'] = $this['shipping_pincode'];

		$master_data['is_shipping_inclusive_tax'] = 0;
		$master_data['is_express_shipping'] = 0;
		$master_data['due_date'] = date("Y-m-d H:i:s", strtotime("+".$this['grace_period_in_days']." days",strtotime($this->app->now)));
		$master_data['round_amount'] = 0;
		$master_data['discount_amount'] = $this->getProDataAmount();
		$master_data['exchange_rate'] = 1;
		$master_data['tnc_id'] = 0;

		$detail_data = [];
		$plan_model = $this->add('xavoc\ispmanager\Model_Plan')->load($this['plan_id']);
		$item = [
					'item_id'=>$plan_model->id,
					'price'=>$plan_model['sale_price'],
					'quantity'=>1,
					'taxation_id'=>$plan_model['tax_id'],
					'shipping_charge'=>0,
					'shipping_duration'=>"",
					'express_shipping_charge'=>0,
					'express_shipping_duration'=>"",
					'qty_unit_id'=>$plan_model['qty_unit_id'],
					'discount'=>0
				];
		array_push($detail_data, $item);
		$qsp_master->createQSP($master_data,$detail_data,"SalesInvoice");
	}

	function getProDataAmount(){
		if(!$this->loaded()) throw new \Exception("radius user must loaded");
		if(!$this->plan_dirty) 0;
		
		return 10;
	}

	function addTopup($topup_id,$date=null){
		$this->setPlan($topup_id,$date,false,true);
	}

	function setPlan($plan, $on_date=null, $remove_old=false,$is_topup=false){

		if(!$on_date) $on_date = isset($this->app->isptoday)? $this->app->isptoday : $this->app->today;


		if(is_numeric($plan)){
			$plan_model = $this->add('xavoc\ispmanager\Model_Plan')->load($plan);
		}
		elseif(is_string($plan)){
			$plan_model = $this->add('xavoc\ispmanager\Model_Plan')->loadBy('name',$plan);
		}
		else
			$plan_model = $plan;

		$this->testDebug('====================','');
		$this->testDebug('Setting Plan '.($remove_old?'(Truncate Old Plan Data)':''), $plan_model['name']. ' on '. $on_date);

		$condition_model = $this->add('xavoc\ispmanager\Model_Condition')->addCondition('plan_id',$plan_model->id);
		
		// set all plan to expire
		if(!$is_topup){
			if($remove_old)
				$update_query = "DELETE FROM  isp_user_plan_and_topup WHERE user_id = '".$this->id."' AND is_topup = '0'";
			else
				$update_query = "UPDATE isp_user_plan_and_topup SET is_expired = '1' WHERE user_id = '".$this->id."' AND is_topup = '0'";
			
			$this->app->db->dsql()->expr($update_query)->execute();
		}

		foreach ($condition_model as $key => $condition) {
			$fields = $condition->getActualFields();
			$unset_field =  ['id','plan_id','plan'];
			$fields = array_diff($fields,$unset_field);

			$u_p = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
			$u_p['user_id'] = $this->id;
			$u_p['plan_id'] = $plan_model->id;
			$u_p['condition_id'] = $condition['id'];
			$u_p['is_topup'] = $plan_model['is_topup'];

			// all fields same as condition are setted
			foreach ($fields as $key => $field_name) {
				$u_p[$field_name] = $condition[$field_name];
			}

			$end_date = date("Y-m-d H:i:s", strtotime("+".$plan_model['plan_validity_value']." ".$plan_model['qty_unit'],strtotime($on_date)));
			
			if($condition['data_reset_value']){

				$reset_date = date("Y-m-d H:i:s", strtotime("+".$condition['data_reset_value']." ".$condition['data_reset_mode'],strtotime($on_date)));

				if($condition['data_reset_mode'] == "months"){
					$reset_date = date('Y-m-01 00:00:00', strtotime($reset_date));
				}elseif ($condition['data_reset_mode'] == "years") {
					$reset_date = date('Y-m-01 00:00:00', strtotime($reset_date));
				}elseif ($condition['data_reset_mode'] == "days") {
					$reset_date = date('Y-m-d 00:00:00', strtotime($reset_date));
				}elseif($condition['data_reset_mode'] == "hours"){
					$reset_date = date('Y-m-d H:00:00', strtotime($reset_date));
				}
			}else{
				$reset_date = null;
			}

			// factor based on implemention
			$u_p['start_date'] = $on_date;						
			$u_p['end_date'] = $end_date;
			$u_p['expire_date'] = $u_p['is_topup']? $end_date : date("Y-m-d H:i:s", strtotime("+".($this['grace_period_in_days']?:'5')." days",strtotime($end_date)));
			$u_p['is_recurring'] = $plan_model['is_auto_renew'];
			$u_p['reset_date'] = $reset_date;
			$u_p['is_effective'] = 0;
			$u_p['data_limit_row'] = null; //id condition has data_limit then set empty else previous data row limit id;
			$u_p->save();
		}

		return $plan_model;
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
		$q=$user_plans->_dsql();
		$q->field('*');
		$q->field($q->expr('data_limit + carry_data net_data_limit'));
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
						(is_expired=0 or is_expired is null)
					)
					OR
					(
						`start_time` is null
					)
					OR (`start_time`='00:00:00' and `end_time`='00:00:00')
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
				AND
					(is_expired=0 or is_expired is null)
				AND
				`user_id`= ". $this->id."
				".
				($with_data_limit? " AND data_limit is not null AND data_limit >0 ":'')
				)
		);

		$q->order('is_topup desc, id desc');
		$q->limit(1);
		$this->testDebug('Querying for '.($with_data_limit?'Data Limit':'Bw Limit').' Row ',null,$q->render());
		$x = $q->getHash();
		return $x;
	}

	function getAAADetails($now=null,$accounting_data=null,$human_redable=false){
		if(!$now) $now = isset($this->app->ispnow)? $this->app->ispnow : $this->app->now;

		$this->testDebug("====================",'');
		if(!$accounting_data)
			$this->testDebug('Authentication on ', $now);
		else
			$this->testDebug('Accounting on ', $now);
		// if accounting data
			// add in effective_row=1
		if($accounting_data){
			if(!is_array($accounting_data)){
				$accounting_data=[$accounting_data,0];
			}

			$condition = "is_effective = 1 AND user_id = ". $this->id;
			$update_query = "UPDATE isp_user_plan_and_topup SET download_data_consumed = IFNULL(download_data_consumed,0) + ".$this->app->human2byte($accounting_data[0]) . " , upload_data_consumed = IFNULL(upload_data_consumed,0) + ".$this->app->human2byte($accounting_data[1]) . " WHERE ". $condition;
			$this->app->db->dsql()->expr($update_query)->execute();
			
			$data=$this->app->db->dsql()->table('isp_user_plan_and_topup')->field('download_data_consumed')->field('upload_data_consumed')->field('remark')->where($this->db->dsql()->expr($condition))->getHash();
			$data['download_data_consumed'] = $this->app->byte2human($data['download_data_consumed']);
			$data['upload_data_consumed'] = $this->app->byte2human($data['upload_data_consumed']);

			$accounting_data['remark']= $data['remark'];

			$this->testDebug('Saving Accounting Data ',$accounting_data,$update_query);
			$this->testDebug('Total Accounting data ',$data);
		}
		
		// run effectiveDataRecord again to set flag in database
		// run getDlUl

		$bw_applicable_row = $this->getApplicableRow($now);
		$this->testDebug('Applicable Row ', $bw_applicable_row['remark'],$bw_applicable_row);

		$data_limit_row = $bw_applicable_row;

		if(!$bw_applicable_row['net_data_limit']) $data_limit_row = $this->getApplicableRow($now,$with_data_limit=true);
		$this->testDebug('Applicable Data Row ', $data_limit_row['remark']);
		
		// Mark datalimitrow as effective
		$this->app->db->dsql()->table('isp_user_plan_and_topup')->set('is_effective',0)->where('user_id',$this->id)->update();
		$this->app->db->dsql()->table('isp_user_plan_and_topup')->set('is_effective',1)->where('id',$data_limit_row['id'])->update();
		$this->testDebug('Mark Effecting for Next Accounting', $data_limit_row['remark'],$data_limit_row);

		// bandwidth or fup ??
		$if_fup='fup_';
		if(($data_limit_row['download_data_consumed'] + $data_limit_row['upload_data_consumed']) < $data_limit_row['net_data_limit']){
			$this->testDebug('Under Data Limit',null,['download_data_consumed'=>$data_limit_row['download_data_consumed'] ,'upload_data_consumed'=> $data_limit_row['upload_data_consumed'],'net_data_limit'=> $data_limit_row['net_data_limit']]);
			$if_fup='';
		}else{
			$this->testDebug('Data Limit Crossed', $this->app->byte2human($data_limit_row['net_data_limit'] - ($data_limit_row['download_data_consumed'] + $data_limit_row['upload_data_consumed'])));
		}

		$dl_field = $if_fup.'download_limit';
		$ul_field = $if_fup.'upload_limit';

		// but from which row ??
		// from applicable if values exists
		$dl_limit = $bw_applicable_row[$dl_field];
		$ul_limit = $bw_applicable_row[$ul_field];

		if($dl_limit !== '') $dl_limit = $data_limit_row[$dl_field];
		if($ul_limit !== '') $ul_limit = $data_limit_row[$ul_field];
		// from data if not 
		// if fup is null or 0 it is a reject authentication command 

		$access= true;
		if(!$dl_limit && !$ul_limit) $access=false;
		
		$final_row = $bw_applicable_row;
		$final_row['dl_limit'] = $dl_limit;
		$final_row['ul_limit'] = $ul_limit;
		$final_row['data_limit'] = $data_limit_row['data_limit'];
		$final_row['carry_data'] = $data_limit_row['carry_data'];
		$final_row['net_data_limit'] = $data_limit_row['net_data_limit'];
		$final_row['download_data_consumed'] = $data_limit_row['download_data_consumed'];
		$final_row['upload_data_consumed'] = $data_limit_row['upload_data_consumed'];
		$final_row['data_limit_row'] = $data_limit_row['remark'];
		$final_row['bw_limit_row'] = $bw_applicable_row['remark'];

		if($human_redable){
			$final_row['data_limit'] = $this->app->byte2human($final_row['data_limit']);
			$final_row['net_data_limit'] = $this->app->byte2human($final_row['net_data_limit']);
			$final_row['dl_limit'] = ($final_row['dl_limit'] !== null ) ? $this->app->byte2human($final_row['dl_limit']):null;
			$final_row['ul_limit'] = ($final_row['ul_limit'] !== null ) ? $this->app->byte2human($final_row['ul_limit']):null;
			$final_row['data_consumed'] = $this->app->byte2human($final_row['download_data_consumed'] + $final_row['upload_data_consumed']);
		}

		return ['access'=>$access, 'result'=>$final_row];
	}

	function setEffectiveDataRecord($now=null){
		if(!$now) $now = isset($this->app->ispnow)? $this->app->ispnow : $this->app->now;

	}

	function cron($date){
		$this->testDebug('====================','');
		$this->testDebug('CRON RUN',$date);
		$this->add('xepan\commerce\Controller_GenerateRecurringInvoice')->run($date);
		$this->add('xavoc\ispmanager\Controller_ResetUserPlanAndTopup')->run($date,$this,$this);

	}

	function testDebug($title,$msg, $details=null){
		if($_GET['testonly']){
			if(is_array($msg)) $msg = print_r($msg,true);
			if(is_array($details)) $details = var_export($details,true);
			/*
			<details>
            	<summary>Getting Started</summary>
            	<p>1. Signup for a free trial</p>
          	</details>
          	*/
          	if($details)
				$this->app->debugisp->add('View')->setHTML('<details><summary><b>'.$title.'</b> '.$msg.'</summary><small><small>'.$details.'</small></small></details>');
			else
				$this->app->debugisp->add('View')->setHTML('<b>'.$title.'</b> '.$msg.'</summary><small><small>'.$details.'</small></small>');
		}
	}

	function page_Topups($page){
		$form = $page->add('Form');
		$form->addField('DropDown','topup')->validate('required')->setEmptyText('Please Select Topup')->setModel($this->add('xavoc\ispmanager\Model_TopUp'));
		$form->addSubmit('Add TopUp');

		$crud = $page->add('CRUD',['allow_edit'=>false,'allow_add'=>false]);
		if($form->isSubmitted()){
			$this->addTopup($form['topup']);
			$form->js(null,$crud->js()->reload())->univ()->successMessage('topup added successfuly')->execute();
		}

		$model = $page->add('xavoc\ispmanager\Model_UserPlanAndTopup');
		$model->addCondition('is_topup',true)->addCondition('user_id',$this->id);
		$model->getElement('plan_id')->caption('TopUp');
		$crud->setModel($model);

	}

	function updateNASCredential(){
		$radcheck_model = $this->add('xavoc\ispmanager\Model_RadCheck');
		$radcheck_model->addCondition('username',$this['radius_username']);
		$radcheck_model->addCondition('attribute',"Cleartext-Password");
		$radcheck_model->addCondition('op', ":=");
		$radcheck_model->tryLoadAny();
		$radcheck_model['value'] = $this['radius_password'];
		$radcheck_model->save();
	}
}