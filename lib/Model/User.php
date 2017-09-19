<?php

namespace xavoc\ispmanager;

class Model_User extends \xepan\commerce\Model_Customer{
	// public $table = "isp_user";
	public $status = ['Active','InActive','Installation','Installed','Won'];
	public $actions = [
				'Won'=>['view','edit','delete','assign_for_installation'],
				'Installation'=>['view','edit','delete','installed','payment_receive'],
				'Installed'=>['view','edit','delete','active'],
				'Active'=>['view','edit','delete','AddTopups','CurrentConditions'],
				'InActive'=>['view','edit','delete','active']
				];
	public $acl_type= "ispmanager_user";
	private $plan_dirty = false;
	private $radius_password_dirty = false;

	public $debug = false;

	function init(){
		parent::init();

		// destroy extra fields
		// $cust_fields = $this->add('xepan\commerce\Model_Customer')->getActualFields();
		$destroy_field = ['assign_to_id','scope','is_designer','score','freelancer_type','related_with','related_id','assign_to','created_by_id','source'];
		foreach ($destroy_field as $key => $field) {
			if($this->hasElement($field))
				$this->getElement($field)->system(true);
		}

		$user_j = $this->join('isp_user.customer_id');

		$user_j->hasOne('xavoc\ispmanager\Plan','plan_id')->display(['form'=>'autocomplete/Basic']);

		$user_j->addField('customer_id'); // added field why not before 
		$user_j->addField('radius_username')->caption('Username');
		$user_j->addField('radius_password')->caption('Password');
		$user_j->addField('simultaneous_use')->type('Number');
		$user_j->addField('grace_period_in_days')->type('number')->defaultValue(0);
		$user_j->addField('custom_radius_attributes')->type('text')->caption('Custom RADIUS Attributes');
		
		$user_j->addField('create_invoice')->type('boolean')->defaultValue(false);
		$user_j->addField('is_invoice_date_first_to_first')->type('boolean')->defaultValue(false);
		$user_j->addField('include_pro_data_basis')->setValueList(['none'=>'None','invoice_only'=>'Invoice Only','data_only'=>'Data Only','invoice_and_data_both'=>'Invoice and Data Both'])->defaultValue('none');
		$user_j->addField('mac_address');
		$user_j->addField('otp_verified')->type('boolean');
		$user_j->addField('otp_send_time')->type('datetime');

		$user_j->addField('last_dl_limit')->defaultValue(0);
		$user_j->addField('last_ul_limit')->defaultValue(0);
		$user_j->addField('last_accounting_dl_ratio')->defaultValue(100);
		$user_j->addField('last_accounting_ul_ratio')->defaultValue(100);

		$user_j->hasMany('xavoc\ispmanager\UserPlanAndTopup','user_id',null,'PlanConditions');
		$user_j->hasMany('xepan\hr\Employee_Document','customer_id',null,'CustomerDocuments');
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

		$user_j->hasOne('xepan\hr\Employee','installation_assign_to_id');
		$user_j->addField('installation_assign_at')->type('date');
		$user_j->addField('installed_at')->type('date');
		$user_j->addField('installed_narration')->type('text');

		$this->addHook('beforeSave',$this);
		$this->addHook('afterSave',$this);
		// $this->addHook('afterSave',[$this,'updateUserConditon']);
		// $this->addHook('afterSave',[$this,'createInvoice']);
		// $this->addHook('afterSave',[$this,'updateNASCredential']);
		// $this->addHook('afterSave',[$this,'updateWebsiteUser']);

		$this->addHook('beforeDelete',$this);
		// $this->is(
		// 		['plan_id|to_trim|required']
		// 	);
	}

	function beforeDelete(){

		try{
			$this->app->db->beginTransaction();

			$this->ref('PlanConditions')->deleteAll();
			$radcheck_model = $this->add('xavoc\ispmanager\Model_RadCheck');
			$radcheck_model->addCondition('username',$this['radius_username']);
			$radcheck_model->each(function($m){
				$m->delete();
			});

			$this->app->db->commit();
		}catch(\Exception $e){
			$this->app->db->rollback();
		}

	}

	function beforeSave(){

		// check unique radius_username 
		if($this['radius_username']){
			$old_model = $this->add('xavoc\ispmanager\Model_User');
			$old_model->addCondition('radius_username',$this['radius_username']);
			if($this->loaded())
				$old_model->addCondition('id','<>',$this['id']);
			$old_model->tryLoadAny();
			if($old_model->loaded())
				throw $this->Exception("(".$this['radius_username'].') radius user is already exist ','ValidityCheck')->setField('radius_username');
		}

		if(!$this['first_name']) $this['first_name'] = $this['radius_username'];
		
		if($this->isDirty('plan_id')){
			$this->plan_dirty = $this->dirty['plan_id'];
		}

		if($this->isDirty('radius_password')){
			$this->radius_password_dirty = 	$this->dirty['radius_password'];
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

		if(!$this['is_invoice_date_first_to_first']){
			$this['include_pro_data_basis'] = 'none';
		}


		if($this['last_ul_limit'] == null OR $this['last_ul_limit'] == 0 OR !is_numeric($this['last_ul_limit']))
			$this['last_ul_limit'] = 0;

		if($this['last_dl_limit'] == null OR $this['last_dl_limit'] == 0 OR !is_numeric($this['last_dl_limit']))
			$this['last_dl_limit'] = 0;

	}

	function afterSave(){
		if($this->radius_password_dirty){
			$this->updateNASCredential();
		}
	}

	function updateUserConditon(){
		if(!$this->plan_dirty OR !$this['plan_id']) return;
		
		$this->setPlan($this['plan_id']);
	}

	function createInvoice($m,$detail_data=null,$false_condition=false,$master_created_at=null){
		if(!$false_condition)
			if(!$this->plan_dirty OR !$this['plan_id'] OR !$this['create_invoice']) return;

		return $this->createQSP($m,$detail_data,'SalesInvoice',null,$master_created_at);
	}

	function createQSP($m,$detail_data=[],$qsp_type="SalesInvoice",$plan_id=null,$master_created_at=null){
		if(is_array($m)) $detail_data = $m;

		if(!$this->loaded()) throw new \Exception("model radius user must loaded");
		$this->reload();

		if(!$this['plan_id'] AND !$this['create_invoice'] AND $qsp_type != "SalesOrder") return;

		$qsp_master = $this->add('xepan\commerce\Model_QSP_Master');
		$master_data = [];

		if($master_created_at){
			$created_at = $master_created_at;
		}elseif($qsp_type == "SalesOrder") {
			$created_at = $this->app->now;
		}
		else{
			$created_at = $this['created_at']?:$this->app->now;
		}
		
		$master_data['qsp_no'] = $this->add('xepan\commerce\Model_'.$qsp_type)->newNumber();
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
		$master_data['created_date'] = $created_at;
		
		$due_date = date("Y-m-d H:i:s", strtotime("+".$this['grace_period_in_days']." days",strtotime($created_at)));
		if(strtotime($created_at) >  strtotime($due_date))
			$due_date = $created_at;
		
		$master_data['due_date'] = $due_date;
		$master_data['round_amount'] = 0;
		$master_data['discount_amount'] = $this->getProDataAmount();
		$master_data['exchange_rate'] = 1;
		$master_data['tnc_id'] = 0;
		
		if(!count($detail_data)){
			$detail_data = [];
			if($plan_id > 0)
				$plan_model = $this->add('xavoc\ispmanager\Model_Plan')->load($plan_id);
			else
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
			
			if( date('d',strtotime($this->app->today)) != 1 && $this['is_invoice_date_first_to_first'] && in_array($this['include_pro_data_basis'], ['invoice_only','invoice_and_data_both'])){
				if($plan_model['renewable_unit'] && $plan_model['renewable_value']){
					$item_renew_date = date("Y-m-01", strtotime("+".$plan_model['renewable_value']." ".$plan_model['renewable_unit'],strtotime($this->app->today)));
					$item_renew_time = strtotime($item_renew_date);
					$invoice_create_time = strtotime($this->app->today);
					$invoice_month_start_time = strtotime(date('Y-m-01',strtotime($this->app->today)));

					$total_days = ceil(abs( $item_renew_time - $invoice_month_start_time ) / (60 * 60 * 24));
					$actual_days = ceil(abs($item_renew_time - $invoice_create_time) / (60 * 60 * 24));

					$one_day_price = $item['price'] / $total_days;
					$actual_price = $one_day_price * $actual_days;
					$item['price'] = $actual_price;

					if($this->debug){
						echo "Invoice Price Pro data----"."<br/>";
						echo "renewable value = ".$plan_model['renewable_value']." ".$plan_model['renewable_unit']."<br/>";
						echo "invoice create date = ".$this->app->today."<br/>";
						echo "invoice month start date = ".date('Y-m-01',strtotime($this->app->today))."<br/>";
						echo "item renew date = ".$item_renew_date."<br/>";
						echo "total days = ".$total_days."<br/>";
						echo "actual days = ".$actual_days."<br/>";
						echo "one_day_price = ".$one_day_price."<br/>";
						echo "actual_price = ".$actual_price."<br/>";
						echo "plan price = ".$plan_model['sale_price']."<br/>";
						echo "--------------"."<br/>";
					}
				}
			}
			array_push($detail_data, $item);
		}

		if($this->debug){
			echo "<pre>";
			print_r($master_data);
			print_r($detail_data);
			echo "</pre>";
		}
		
		return $qsp_master->createQSP($master_data,$detail_data,$qsp_type);
	}

	function getProDataAmount(){
		if(!$this->loaded()) throw new \Exception("radius user must loaded");
		
		return 0;
	}

	function addTopup($topup_id,$date=null,$remove_old_topups=false){
		$this->setPlan($topup_id,$date,false,true,$remove_old_topups);
	}

	function setPlan($plan, $on_date=null, $remove_old=false,$is_topup=false,$remove_old_topups=false){
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
		$this->testDebug(($is_topup?'Adding Topup ':'Setting Plan ').($remove_old?'(Truncate Old Plan Data)'.($remove_old_topups?' (Removing old topups also)':''):''), $plan_model['name']. ' on '. $on_date);

		$condition_model = $this->add('xavoc\ispmanager\Model_Condition')
							->addCondition('plan_id',$plan_model->id);

		// setting same plan again then only update the existing condition
		if($this['plan_id'] != $plan_model->id){
			if(!$is_topup){
				if($remove_old)
					$update_query = "DELETE FROM  isp_user_plan_and_topup WHERE user_id = '".$this->id."' AND is_topup = '0'";
				else
					$update_query = "UPDATE isp_user_plan_and_topup SET is_expired = '1' WHERE user_id = '".$this->id."' AND is_topup = '0'";
				
				$this->app->db->dsql()->expr($update_query)->execute();
			}

			if($remove_old_topups){
				$update_query = "DELETE FROM  isp_user_plan_and_topup WHERE user_id = '".$this->id."' AND is_topup = '1'";
				$this->app->db->dsql()->expr($update_query)->execute();
			}
		}

		
						
		foreach ($condition_model as $key => $condition) {
			
			$fields = $condition->getActualFields();
			$unset_field =  ['id','plan_id','plan'];
			$fields = array_diff($fields,$unset_field);

			$u_p = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
			$u_p->addCondition('user_id',$this->id)
				->addCondition('plan_id',$plan_model->id)
				->addCondition('condition_id',$condition['id'])
				;
			$u_p->tryLoadAny();

			if(!$u_p->loaded()){
				$u_p['is_effective'] = 0;
			}
			// $u_p['user_id'] = $this->id;
			// $u_p['plan_id'] = $plan_model->id;
			// $u_p['condition_id'] = $condition['id'];
			$u_p['is_topup'] = $plan_model['is_topup'];

			// all fields same as condition are setted
			foreach ($fields as $key => $field_name) {
				$u_p[$field_name] = $condition[$field_name];
			}

			$end_date = date("Y-m-d H:i:s", strtotime("+".$plan_model['plan_validity_value']." ".$plan_model['qty_unit'],strtotime($on_date)));
			
			// set end date last
			if($this['is_invoice_date_first_to_first']){
				$end_date = date("Y-m-t H:i:s", strtotime($end_date));
			}

			if($condition['data_reset_value']){

				$reset_date = date("Y-m-d H:i:s", strtotime("+".$condition['data_reset_value']." ".$condition['data_reset_mode'],strtotime($on_date)));

				if($condition['data_reset_mode'] == "months"){
					if($this['is_invoice_date_first_to_first'])
						$reset_date = date('Y-m-01 00:00:00', strtotime($reset_date));
					else
						$reset_date = date('Y-m-d 00:00:00', strtotime($reset_date));

				}elseif ($condition['data_reset_mode'] == "years") {
					if($this['is_invoice_date_first_to_first'])
						$reset_date = date('Y-m-01 00:00:00', strtotime($reset_date));
					else					
						$reset_date = date('Y-m-d 00:00:00', strtotime($reset_date));
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
			
			$u_p['data_limit_row'] = null; //id condition has data_limit then set empty else previous data row limit id;
			
			// pro data update data_limit
			if( $condition['is_pro_data_affected'] && $this['is_invoice_date_first_to_first'] && in_array($this['include_pro_data_basis'], ['data_only','invoice_and_data_both']) && $reset_date){
				$end_time = strtotime(date('Y-m-d',strtotime($reset_date)));
				$day_first_start_time = strtotime(date('Y-m-01',strtotime($on_date)));
				$actual_start_time = strtotime($on_date);

				$total_days = ceil(abs( $end_time - $day_first_start_time ) / (60 * 60 * 24));
				$actual_days = ceil(abs($end_time - $actual_start_time) / (60 * 60 * 24));

				if($total_days != $actual_days){
					$one_day_limit = $this->app->human2byte($condition['data_limit']) / $total_days;
					$pro_data_limit = $actual_days * $one_day_limit;
					$u_p['data_limit'] = $pro_data_limit;
				}

				if($_GET['debug']){
				echo "set Plan =----------"."<br/>";
					echo "reset Date = ".$reset_date."<br/>";
					echo "on Date = ".$on_date."<br/>";
					echo "actual_days= ".$actual_days."</br>";
					echo "total_days= ".$total_days."</br>";
					echo "total limit = ".$condition['data_limit']."</br>";
					echo "actual limit = ".$pro_data_limit."</br>";
				echo "------------------"."<br/>";
				}
			}

			$u_p->save();
		}

		$this['last_dl_limit']=null;
		$this['last_ul_limit']=null;
		$this->save();
		return $plan_model;
	}


	// site-enables/default.conf file simulated
	function getAAADetails($now=null,$accounting_data=null,$accounting_time=0,$human_redable=false){
		
		if(!$now) $now = isset($this->app->ispnow)? $this->app->ispnow : $this->app->now;
		$username = $this['radius_username'];

		if($accounting_data){
			if(!is_array($accounting_data)){
				$accounting_data=[$accounting_data,0];
			}

			$dl_data = $this->human2byte($accounting_data[0]);
			$ul_data = $this->human2byte($accounting_data[1]);

			$result = $this->runQuery("SELECT updateAccountingData($dl_data,$ul_data,'$now','$username',$accounting_time)",true);
		}else{
			$result = $this->runQuery("SELECT checkAuthentication('$now','$username')",true);
		}

		$result_array= explode(",", $result);
		$limit_array = explode("/", $result_array[2]);
		if(!isset($limit_array[1])) $limit_array[1]=0;
		return ['access'=>$result_array[0], 'coa'=>$result_array[1],'dl_limit'=>$limit_array[0],'ul_limit'=>$limit_array[1]];

	}

	function canAccess(){
		return $this->getAAADetails()['access'];
	}

	function runQuery($query, $gethash=false){
		if($gethash){
			return $this->app->db->dsql()->expr($query)->getHash();
		}else{
			return $this->app->db->dsql()->expr($query)->execute();
		}
	}

	function byte2human($bytes, $decimal =2){
		return $this->app->byte2human($bytes, $decimal);
	}

	function human2byte($value){
		return $this->app->human2byte($value);
	}


	// function getAAADetails($now=null,$accounting_data=null,$human_redable=false){
	// 	if(!$now) $now = isset($this->app->ispnow)? $this->app->ispnow : $this->app->now;

	// 	$day = strtolower(date("D", strtotime($now)));

	// 	$this->testDebug("====================",'');
	// 	if(!$accounting_data)
	// 		$this->testDebug('Authentication on ', $now . " [ $day ]");
	// 	else
	// 		$this->testDebug('Accounting on ', $now . " [ $day ]");
	// 	// if accounting data
	// 		// add in effective_row=1
	// 	if($accounting_data){
	// 		if(!is_array($accounting_data)){
	// 			$accounting_data=[$accounting_data,0];
	// 		}

	// 		$condition = "is_effective = 1 AND user_id = ". $this->id;
	// 		$update_query = "UPDATE isp_user_plan_and_topup SET download_data_consumed = IFNULL(download_data_consumed,0) + ".($this->human2byte($accounting_data[0])*$this['last_accounting_dl_ratio']/100) . " , upload_data_consumed = IFNULL(upload_data_consumed,0) + ".($this->human2byte($accounting_data[1])*$this['last_accounting_ul_ratio']/100) . " WHERE ". $condition;
	// 		$this->app->db->dsql()->expr($update_query)->execute();
			
	// 		$data = $this->app->db->dsql()->table('isp_user_plan_and_topup')->field('download_data_consumed')->field('upload_data_consumed')->field('remark')->where($this->db->dsql()->expr($condition))->getHash();
	// 		$data['download_data_consumed'] = $this->byte2human($data['download_data_consumed']);
	// 		$data['upload_data_consumed'] = $this->byte2human($data['upload_data_consumed']);

	// 		$accounting_data['remark']= $data['remark'];
	// 		$accounting_data['dl_ratio']= $this['last_accounting_dl_ratio'];
	// 		$accounting_data['ul_ratio']= $this['last_accounting_ul_ratio'];

	// 		$this->testDebug('Saving Accounting Data ',$accounting_data,$update_query);
	// 		$this->testDebug('Total Accounting data ',$data);
	// 	}
	// 	// --------------------- end of accounting

	// 	$bw_applicable_row = $this->getApplicableRow($now);
	// 	$this->testDebug('Applicable Row ', $bw_applicable_row['remark'],$bw_applicable_row);
	// 	// run effectiveDataRecord again to set flag in database
	// 	// run getDlUl
	// 	// echo $bw_applicable_row['net_data_limit']." = ".$bw_applicable_row['download_data_consumed'] ." + ".$bw_applicable_row['upload_data_consumed']."<br/>";
	// 	$data_limit_row = $bw_applicable_row;

	// 	if(!$bw_applicable_row['net_data_limit']) $data_limit_row = $this->getApplicableRow($now,$with_data_limit=true);
	// 	$this->testDebug('Applicable Data Row ', $data_limit_row['remark']);

	// 	// bandwidth or fup ??
	// 	$if_fup='fup_';
	// 	if(($data_limit_row['download_data_consumed'] + $data_limit_row['upload_data_consumed']) < $data_limit_row['net_data_limit']){
	// 		$this->testDebug('Under Data Limit',null,['download_data_consumed'=>$data_limit_row['download_data_consumed'] ,'upload_data_consumed'=> $data_limit_row['upload_data_consumed'],'net_data_limit'=> $data_limit_row['net_data_limit']]);
	// 		$if_fup='';
	// 	}else{
	// 		// this is 'this line'
	// 		// if trat_ cegckbox is on {
	// 			// find another data_limit_row
	// 				// if that is also consumed use that lines fup 
	// 				// else use this line's fup as main data limit 
	// 		// }
	// 		if($bw_applicable_row['treat_fup_as_dl_for_last_limit_row']){
				
	// 			$next_data_limit_row = $this->getApplicableRow($now,null,$data_limit_row['id']);
	// 			// echo "old id ".$data_limit_row['id']."<br/>";
	// 			// echo "new id ".$next_data_limit_row['id']."<br/>";

	// 			if( ($next_data_limit_row['download_data_consumed'] + $next_data_limit_row['upload_data_consumed']) > $next_data_limit_row['net_data_limit'] ){
	// 				$data_limit_row['download_limit'] = $next_data_limit_row['fup_download_limit'];
	// 				$data_limit_row['upload_limit'] = $next_data_limit_row['fup_upload_limit'];
	// 				$data_limit_row['remark'] = $next_data_limit_row['remark'];
	// 				// echo "next fup"."<br/>";
	// 			}else{

	// 				$data_limit_row['download_limit'] = $bw_applicable_row['fup_download_limit'];
	// 				$data_limit_row['upload_limit'] = $bw_applicable_row['fup_upload_limit'];
	// 				$data_limit_row['remark'] = $next_data_limit_row['remark'];
	// 				// echo "old ".$next_data_limit_row['remark']."<br/>";
	// 			}
	// 		}

	// 		$this->testDebug('Data Limit Crossed', $this->byte2human($data_limit_row['net_data_limit'] - ($data_limit_row['download_data_consumed'] + $data_limit_row['upload_data_consumed'])));
	// 	}

	// 	// Mark datalimitrow as effective
	// 	$this->app->db->dsql()->table('isp_user_plan_and_topup')->set('is_effective',0)->where('user_id',$this->id)->update();
	// 	$q=$this->app->db->dsql()->table('isp_user_plan_and_topup')->set('is_effective',1)->where('id',$data_limit_row['id']);
	// 	$q->update();
	// 	$this->testDebug('Mark Effecting for Next Accounting', $data_limit_row['remark'],['data_limit_row'=>$data_limit_row, 'query'=>$q->getDebugQuery($q->render())]);


	// 	$dl_field = $if_fup.'download_limit';
	// 	$ul_field = $if_fup.'upload_limit';

	// 	// but from which row ??
	// 	// from applicable if values exists
	// 	$dl_limit = $bw_applicable_row[$dl_field];
	// 	$ul_limit = $bw_applicable_row[$ul_field];

	// 	if($dl_limit === null) $dl_limit = $data_limit_row[$dl_field];
	// 	if($ul_limit === null) $ul_limit = $data_limit_row[$ul_field];
	// 	// from data if not 
	// 	// if fup is null or 0 it is a reject authentication command

	// 	$access= true;
	// 	if(!$dl_limit && !$ul_limit) $access=false;

		
	// 	$final_row = $bw_applicable_row;
	// 	$final_row['dl_limit'] = $dl_limit;
	// 	$final_row['ul_limit'] = $ul_limit;
	// 	$final_row['data_limit'] = $data_limit_row['data_limit'];
	// 	$final_row['carry_data'] = $data_limit_row['carry_data'];
	// 	$final_row['net_data_limit'] = $data_limit_row['net_data_limit'];
	// 	$final_row['download_data_consumed'] = $data_limit_row['download_data_consumed'];
	// 	$final_row['upload_data_consumed'] = $data_limit_row['upload_data_consumed'];
	// 	$final_row['data_limit_row'] = $data_limit_row['remark'];
	// 	$final_row['bw_limit_row'] = $bw_applicable_row['remark'];
		
	// 	$final_row['coa'] = false;
		
	// 	if(!$accounting_data OR ($accounting_data !==null && ($dl_limit !== $this['last_dl_limit'] || $ul_limit !== $this['last_ul_limit'] || !$access))){
	// 		// echo "cur dl limit = ".$dl_limit." last dl limit = ".$this['last_dl_limit']."<br/>";
	// 		// echo "cur ul limit = ".$dl_limit." last ul limit = ".$this['last_ul_limit']."<br/>";
	// 		$final_row['coa'] = true;
	// 		$this['last_dl_limit'] = $dl_limit;
	// 		$this['last_ul_limit'] = $ul_limit;
	// 		$this->save();
	// 		$this->testDebug('Saving Dl/UL Limits', 'dl '.$dl_limit.', ul '. $ul_limit);
	// 	}

	// 	if($this['last_accounting_dl_ratio'] != $bw_applicable_row['accounting_download_ratio'] || $this['last_accounting_ul_ratio'] != $bw_applicable_row['accounting_upload_ratio']){
	// 		$final_row['coa'] = true;
	// 		$this['last_accounting_dl_ratio'] = $bw_applicable_row['accounting_download_ratio'];
	// 		$this['last_accounting_ul_ratio'] = $bw_applicable_row['accounting_upload_ratio'];
	// 		$this->testDebug('Saving Dl/UL Ratio for next accounting data', 'dl '.$bw_applicable_row['accounting_download_ratio'].', ul '. $bw_applicable_row['accounting_upload_ratio']);
	// 		$this->save();
	// 	}
			

	// 	if($human_redable){
	// 		$final_row['data_limit'] = $this->byte2human($final_row['data_limit']);
	// 		$final_row['net_data_limit'] = $this->byte2human($final_row['net_data_limit']);
	// 		$final_row['dl_limit'] = ($final_row['dl_limit'] !== null ) ? $this->byte2human($final_row['dl_limit']):null;
	// 		$final_row['ul_limit'] = ($final_row['ul_limit'] !== null ) ? $this->byte2human($final_row['ul_limit']):null;
	// 		$final_row['data_consumed'] = $this->byte2human($final_row['download_data_consumed'] + $final_row['upload_data_consumed']);
	// 	}

	// 	return ['access'=>$access, 'result'=>$final_row];
	// }

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

	function page_AddTopups($page){
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
		if(!$this['radius_password']) throw new \Exception("radius password is not defined");

		$radcheck_model = $this->add('xavoc\ispmanager\Model_RadCheck');
		$radcheck_model->addCondition('username',$this['radius_username']);
		$radcheck_model->addCondition('attribute',"Cleartext-Password");
		$radcheck_model->addCondition('op', ":=");
		$radcheck_model->tryLoadAny();
		$radcheck_model['value'] = $this['radius_password'];
		$radcheck_model->saveAndUnload();
	}

	function page_CurrentConditions($page){
		$crud = $page->add('xepan\hr\CRUD');

		if($crud->isEditing()){
			$form = $crud->form;
			$form->add('xepan\base\Controller_FLC')
				->addContentSpot()
				// ->makePanelsCoppalsible()
				->layout([
						'plan_id'=>'About Plan~c1~3',
						'condition_id'=>'c11~3',
						'remark'=>'c12~3',
						'is_topup'=>'c13~3',
						'data_limit'=>'c2~6',
						'carry_data'=>'c21~6',
						'download_limit'=>'DL/UL Limit~c1~3~in KBps',
						'upload_limit'=>'c11~3~in KBps',
						'fup_download_limit'=>'c12~3~in KBps',
						'fup_upload_limit'=>'c13~3~in KBps',
						'accounting_download_ratio'=>'c2~6~Ratio in %',
						'accounting_upload_ratio'=>'c21~6~Ratio in %',
						'start_date'=>'Dates~c1~3',
						'end_date'=>'c11~3',
						'expire_date'=>'c12~3',
						'is_expired'=>'c13~3',
						'is_recurring'=>'c2~3',
						'is_effective'=>'c21~3',
						'download_data_consumed'=>'Data Consumed~c1~6~in MB',
						'upload_data_consumed'=>'c2~6~in MB',
						'time_limit'=>'Time Limit~c1~3',
						'data_limit_row'=>'c11~3',
						'duplicated_from_record_id'=>'c12~3',
						'is_data_carry_forward'=>'c13~3',
						'start_time'=>'Time~c1~6',
						'end_time'=>'c2~6',
						'reset_date'=>'Reset Box~c1~3',
						'data_reset_value'=>'c2~3',
						'data_reset_mode'=>'c3~6',
						'sun'=>'Week~c1~1',
						'mon'=>'c2~1',
						'tue'=>'c3~1',
						'wed'=>'c4~1',
						'thu'=>'c5~1',
						'fri'=>'c6~1',
						'sat'=>'c7~1',
						'd01'=>'Days~c1~1',
						'd02'=>'c2~1',
						'd03'=>'c3~1',
						'd04'=>'c4~1',
						'd05'=>'c5~1',
						'd06'=>'c6~1',
						'd07'=>'c7~1',
						'd08'=>'c8~1',
						'd09'=>'c9~1',
						'd10'=>'c10~1',
						'd11'=>'c11~1',
						'd12'=>'c12~1',
						'd13'=>'c13~1',
						'd14'=>'c14~1',
						'd15'=>'c15~1',
						'd16'=>'c16~1',
						'd17'=>'c17~1',
						'd18'=>'c18~1',
						'd19'=>'c19~1',
						'd20'=>'c20~1',
						'd21'=>'c21~1',
						'd22'=>'c22~1',
						'd23'=>'c23~1',
						'd24'=>'c24~1',
						'd25'=>'c25~1',
						'd26'=>'c26~1',
						'd27'=>'c27~1',
						'd28'=>'c28~1',
						'd29'=>'c29~1',
						'd30'=>'c30~1',
						'd31'=>'c31~1',
						'treat_fup_as_dl_for_last_limit_row'=>'MISC~c1~6',
						'is_pro_data_affected'=>'c2~6',
						'burst_dl_limit'=>'Burst~c1~3~limit per second',
						'burst_ul_limit'=>'c11~3~limit per second',
						'burst_threshold_dl_limit'=>'c12~3~limit per second',
						'burst_threshold_ul_limit'=>'c13~3~limit per second',
						'burst_dl_time'=>'c2~3~time in second',
						'burst_ul_time'=>'c21~3~time in second',
						'priority'=>'c22~6',
				]);
						
		}
		$model = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
		$model->addCondition('user_id',$this->id);
		$crud->setModel($model);

	}

	// function updateQSPBeforeSave($app,$master_data,$detail_data,$type){
	// 	echo $type;
	// 	echo "<pre>";
	// 	print_r($master_data);
	// 	print_r($detail_data);
	// 	echo "</pre>";
	// 	die();
	// }

	function import($data){
		// get all plan list
		$plan_list = [];
		foreach ($this->add('xavoc\ispmanager\Model_Plan')->getRows() as $key => $plan) {
			$plan_list[strtolower(trim($plan['name']))] = $plan['id'];
		}
		
		// get all country list
		$country_list = [];
		foreach ($this->add('xepan\base\Model_Country') as $key => $country) {
			$country_list[strtolower(trim($country['name']))] = $country['id'];
		}

		$state_list = [];
		$state_model = $this->add('xepan\base\Model_State');
		foreach ($state_model as $key => $state) {
			$state_list[strtolower(trim($state['name']))] = $state['id'];
		}

		// echo "<pre>";
		// print_r($plan_list);
		// print_r($country_list);
		// print_r($state_list);
		// echo "</pre>";
		// die();

		try{
			$this->api->db->beginTransaction();
			foreach ($data as $key => $record) {
				$user = $this->add('xavoc\ispmanager\Model_User');
				// adding hook
				$user->addHook('afterSave',[$user,'updateUserConditon']);
				$user->addHook('afterSave',[$user,'createInvoice']);
				$user->addHook('afterSave',[$user,'updateNASCredential']);
				$user->addHook('afterSave',[$user,'updateWebsiteUser']);

				$user->addCondition('radius_username',trim($record['RADIUS_USERNAME']));
				$user->tryLoadAny();

				$plan_name = strtolower(trim($record['PLAN']));

				$plan_id = isset($plan_list[$plan_name])?$plan_list[$plan_name]:0;
				$user['plan_id'] = $plan_id;
				
				$country_name = strtolower(trim($record['COUNTRY']));
				$country_id = isset($country_list[$country_name])?$country_list[$country_name]:0;
				$user['country_id'] = $country_id;

				$state_name = strtolower(trim($record['STATE']));
				$state_id = isset($state_list[$state_name])?$state_list[$state_name]:0;
				$user['state_id'] = $state_id;
				
				foreach ($record as $field => $value) {
					$field_name = strtolower(trim($field));
					$user[$field_name] = $value;
				}
				$user['created_at'] = date('Y-m-d H:i:s',strtotime($record['CREATED_AT']))?:$this->app->now;
				// $user['created_at'] = date('Y-m-d H:i:s',strtotime($record['INVOICE_DATE']))?:$this->app->now;
				if(!strlen(trim($user['first_name'])))
					$user['first_name'] = $user['radius_username'];
				
				$user->save();
				// update email and phone number
				if($record['MOBILE']){
					$cp = $this->add('xepan\base\Model_Contact_Phone');
					$cp['head'] = 'Official';
					$cp['contact_id'] = $user->id;
					$cp['value'] = $record['MOBILE'];
					$cp->save();
				}
				if($record['PHONE']){
					$cp = $this->add('xepan\base\Model_Contact_Phone');
					$cp['head'] = 'Official';
					$cp['contact_id'] = $user->id;
					$cp['value'] = $record['PHONE'];
					$cp->save();
				}

				if($record['EMAIL'] AND filter_var($record['EMAIL'],FILTER_VALIDATE_EMAIL)){
					$ce = $this->add('xepan\base\Model_Contact_Email');
					$ce['head'] = 'Official';
					$ce['contact_id'] = $user->id;
					$ce['value'] = $record['EMAIL'];
					$ce->save();
				}


				// data_Remark: eg.dl/up/remark, 1039/209/MainPlan,3089/Topupplan
				if($record['DATA_CONSUMED']){
					$condition_consumed_list = explode(",", $record['DATA_CONSUMED']);

					foreach ($condition_consumed_list as $key => $c_c) {
						$consumed_condition = explode("/", $c_c);
						if(count($consumed_condition) != 3 ) continue;
						$dl_data_consumed = $consumed_condition[0];
						$up_data_consumed = $consumed_condition[1];
						$remark = trim($consumed_condition[2]);

						$upt = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
						$upt->addCondition('user_id',$user->id);
						$upt->addCondition('plan_id',$plan_id);
						$upt->addCondition('remark',$remark);
						$upt->tryLoadAny();
						if(!$upt->loaded()) continue;

						$upt['download_data_consumed'] = $dl_data_consumed;
						$upt['upload_data_consumed'] = $up_data_consumed;
						$upt->save();
					}	
				}

			}

			$this->api->db->commit();

		}catch(\Exception $e){
			$this->api->db->rollback();
			throw new \Exception($e->getMessage());
		}
	}

	function updateWebsiteUser(){
		if(!$this['radius_username']) return;
		
		$username = trim($this['radius_username']);
		if($this->app->getConfig('username_is_email',true)){
			if(!filter_var($username, FILTER_VALIDATE_EMAIL)){
				$username .= "@isp-fake.com";
			}
		}	

		$user = $this->add('xepan\base\Model_User');
		$user->addCondition('scope','WebsiteUser');
		$user->addCondition('username',$username);
		$user->tryLoadAny();

		$user_id = $this['user_id'];
		if($this['id']){
			$r_user = $this->add('xavoc\ispmanager\Model_User')
						->load($this['id']);
			$user_id = $r_user['user_id'];
		}

		if($user->loaded() && $user->id != $user_id)
			throw new \Exception("(".$user->id."=".$username." = ".$this->id.") user name already use with other isp user ");
		
		// $user=$this->add('xepan\base\Model_User');
		$this->add('BasicAuth')
			->usePasswordEncryption('md5')
			->addEncryptionHook($user);
		$user['password'] = $this['radius_password'];
		$user->save();
		
		$this['user_id'] = $user->id;
		$this->save();
	}

	// online invoice paid check / then associated plan with it
	function invoicePaid($app,$invoice_model){
		
		$customer = $this->add('xavoc\ispmanager\Model_User');
		$customer->addCondition('id',$invoice_model['contact_id']);
		$customer->tryLoadAny();

		if(!$customer->loaded()) throw new \Exception("customer not found");

		// // $user->addCondition('customer_id',$customer->id)->tryLoadAny();
		// // throw new \Exception($user->id);
		
		$user = $this->add('xavoc\ispmanager\Model_User');
		$user->loadBy('radius_username',$customer['radius_username']);

		$items = $invoice_model->Items()->tryLoadAny();
		if($items->loaded())
			$user->setPlan($items['item_id']);
	}

	function addAttachment($attach_id,$type=null){
		if(!$attach_id) return;
		$attach = $this->add('xepan\hr\Model_Employee_Document');
		$attach['employee_document_id'] = $attach_id;
		$attach['employee_id'] = $this->id;
		$attach['type'] = $type;	
		$attach->save();

		return $attach;
	}

	function getAttachments($urls=true){
		$attach_arry = array();
		if($this->loaded()){
			$attach_m = $this->add('xepan\hr\Model_Employee_Document');
			$attach_m->addCondition('employee_id',$this->id);
			foreach ($attach_m as $attach) {
				$attach_arry[] = $urls?$attach['file']:$attach['id'];
			}

		}
		
		return $attach_arry;
	}

	function getCurrentCondition(){
		if(!$this->loaded())  return ['status'=>'no record found'];

		$upt = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
		$upt->addCondition('plan_id',$this['plan_id']);
		$upt->addCondition('user_id',$this->id);
		$upt->addCondition('is_effective',true);
		return $upt->getRows();
	}


	function page_assign_for_installation($page){
		$form = $page->add('Form');
		$form->setModel($this,['installation_assign_to_id','installed_narration']);
		$form->addSubmit('Assign Now');
		$form->getElement('installation_assign_to_id')->validate('required');
		
		if($form->isSubmitted()){
			$this->assignForInstallation($form['installation_assign_to_id'],$form['installed_narration']);
			return $this->app->page_action_result = $this->app->js(true,$page->js()->univ()->closeDialog())->univ()->successMessage('assign for installation');
		}
	}

	function assignForInstallation($installation_assign_to_id,$installed_narration=null){
		$this['installation_assign_to_id'] = $installation_assign_to_id;
		$this['installed_narration'] = $installed_narration;
		$this['installation_assign_at'] = $this->app->now;
		$this['status'] = "Installation";
		$this->save();

		$employee = $this->add('xavoc\ispmanager\Model_Employee');
		$employee->load($this['installation_assign_to_id']);

		// $this->app->employee
		// 		->addActivity("Lead '".$this['code']."' assign to employee '".$employee['name']." for installation"."'",null, $this['installation_assign_to_id'] /*Related Contact ID*/,null,null,null)
		// 		->notifyWhoCan('installed','Installation')
		// 		->notifyTo([$this['installation_assign_to_id'],$this['created_by_id']],"Lead '" . $this['code'] ."' Assign to Employee '".$employee['name']." for installation '")
		// 		;
		return $this;
	}
	
	function page_payment_receive($page){

		$form = $page->add('Form');
		$payment_mode_field = $form->addField('DropDown','payment_mode')->setValueList(['Cash'=>'Cash','Cheque'=>'Cheque','DD'=>'DD']);
		$payment_mode_field->setEmptyText('select payment mode');
		$form->addField('Number','cheque_no')->set(0);
		$form->addField('DatePicker','cheque_date');
		$form->addField('Number','dd_no')->set(0);
		$form->addField('DatePicker','dd_date');
		$form->addField('text','bank_detail');
		$form->addField('number','amount')->set(0);
		$form->addField('text','narration');

		$payment_mode_field->js(true)->univ()->bindConditionalShow([
				'Cash'=>['amount','narration'],
				'Cheque'=>['cheque_no','cheque_date','bank_detail','amount','narration'],
				'DD'=>['dd_no','dd_date','bank_detail','amount','narration'],
			],'div.atk-form-row');

		$form->addSubmit('Payment Receive');
		if($form->isSubmitted()){

			$p_field_array = [
						'Cash'=>['amount'],
						'Cheque'=>['cheque_no','cheque_date','bank_detail','amount','narration'],
						'DD'=>['dd_no','dd_date','bank_detail','amount','narration']
				];

			$payment_detail = [];
			if($form['payment_mode'] == "Cash"){
				if(!$form['amount']) $form->error('amount','must not be empty');

				$payment_detail = [
									'payment_mode'=>'Cash',
									'amount'=>$form['amount'],
									'narration'=>$form['narration']
								];
			}

			if($form['payment_mode'] == "Cheque"){

				if(!$form['cheque_no']) $form->error('cheque_no','must not be empty');
				if(!$form['cheque_date']) $form->error('cheque_date','must not be empty');
				if(!$form['bank_detail']) $form->error('bank_detail','must not be empty');
				if(!$form['amount']) $form->error('amount','must not be empty');
				
				$payment_detail = [
									'payment_mode'=>'Cheque',
									'cheque_no'=>$form['cheque_no'],
									'cheque_date'=>$form['cheque_date'],
									'bank_detail'=>$form['bank_detail'],
									'amount'=>$form['amount'],
									'narration'=>$form['narration']
								];
			}

			if($form['payment_mode'] == "DD"){
				if(!$form['dd_no']) $form->error('dd_no','must not be empty');
				if(!$form['dd_date']) $form->error('dd_date','must not be empty');
				if(!$form['bank_detail']) $form->error('bank_detail','must not be empty');
				if(!$form['amount']) $form->error('amount','must not be empty');

				$payment_detail = [
									'payment_mode'=>'DD',
									'dd_no'=>$form['dd_no'],
									'dd_date'=>$form['dd_date'],
									'bank_detail'=>$form['bank_detail'],
									'amount'=>$form['amount'],
									'narration'=>$form['narration']
							];
			}
			
			$this->payment_receive($payment_detail);
			return $this->app->page_action_result = $this->app->js(true,$page->js()->univ()->closeDialog())->univ()->successMessage('Payment Received');
		}
	}

	function payment_receive($detail_array){
		if(!count($detail_array)) return;

		$payment = $this->add('xavoc\ispmanager\Model_PaymentTransaction');
		foreach ($detail_array as $field => $value) {
			$payment[$field] = $value;
		}
		$payment['contact_id'] = $this->id;
		$payment['employee_id'] = $this->app->employee->id;
		$payment->save();
		return $payment;
	}

	function installed(){
		$this['status'] = "Installed";
		$this->save();
		
		$employee = $this->add('xavoc\ispmanager\Model_Employee');
		$employee->load($this['installation_assign_to_id']);

		$msg = "Installation Complete at lead '".$this['code']."' by '".$employee['name'];
		$this->app->employee
				->addActivity($msg,null, $this['installation_assign_to_id'] /*Related Contact ID*/,null,null,null)
				->notifyWhoCan('active','Installed')
				;
		return $this;
	} 

	function page_active($page){

		$mandatory_field = [
						'radius_username'=>'required',
						'radius_password'=>'required',
						'plan_id'=>'required',
						];
		$form = $page->add('xavoc\ispmanager\Form_CAF',['model'=>$this,'mandatory_field'=>$mandatory_field]);

		if(!$this['radius_username'])
			$form->getElement('radius_username')->set($this['code']);
		// $form = $page->add('Form');
		// $form->setModel($this,['plan_id','radius_username','radius_password','is_invoice_date_first_to_first','create_invoice','include_pro_data_basis']);
		// $form->addSubmit('Create User and Activate Plan');
		// if($form->isSubmitted()){
		$form->addHook('CAF_AfterSave',function($form)use($page){
			$this->active();
			return $this->app->page_action_result = $this->app->js(true,$page->js()->univ()->closeDialog())->univ()->successMessage('User Activated');
		});
		$form->process();
	}

	function active(){
		$this->setPlan($this['plan_id']);
		$this['status'] = 'Active';
		$this->save();

		// $this->updateUserConditon();
		$this->createInvoice($this);
		$this->updateNASCredential();
		$this->updateWebsiteUser();
	}


}
