<?php

namespace xavoc\ispmanager;

class Model_User extends \xepan\commerce\Model_Customer{ 
	// public $table = "isp_user";
	public $status = ['Active','InActive'];
	public $actions = [
				'Active'=>['view','edit','delete','AddTopups','CurrentConditions'],
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
		$user_j->addField('is_invoice_date_first_to_first')->type('boolean')->defaultValue(false);
		$user_j->addField('include_pro_data_basis')->setValueList(['none'=>'None','invoice_only'=>'Invoice Only','data_only'=>'Data Only','invoice_and_data_both'=>'Invoice and Data Both'])->defaultValue('none');

		$user_j->addField('last_dl_limit');
		$user_j->addField('last_ul_limit');
		$user_j->addField('last_accounting_dl_ratio');
		$user_j->addField('last_accounting_ul_ratio');

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
		if($this->isDirty('plan_id')){
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

		if(!$this['is_invoice_date_first_to_first']){
			$this['include_pro_data_basis'] = 'none';
		}
	}

	function updateUserConditon(){
		if(!$this->plan_dirty OR !$this['plan_id']) return;
		
		$this->setPlan($this['plan_id']);
	}

	function createInvoice(){

		if(!$this->loaded()) throw new \Exception("model radius user must loaded");
		$this->reload();

		if(!$this['plan_id'] AND !$this['create_invoice'] ) return;

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
		$master_data['discount_amount'] = 0;
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

				if($_GET['debug']){
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
		if($_GET['debug']){
			echo "<pre>";
			print_r($master_data);
			print_r($detail_data);
			echo "</pre>";
		}
		$qsp_master->createQSP($master_data,$detail_data,"SalesInvoice");
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

		$condition_model = $this->add('xavoc\ispmanager\Model_Condition')->addCondition('plan_id',$plan_model->id);
		
		// set all plan to expire
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
			$u_p['is_effective'] = 0;
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
	function getAAADetails($now=null,$accounting_data=null,$human_redable=false){
		
		if(!$now) $now = isset($this->app->ispnow)? $this->app->ispnow : $this->app->now;

		// ===== DB.php =========
		$username = $this['radius_username'];

		$user_query = "SELECT * from isp_user where radius_username = '$username'";
		$user_data = $this->app->db->dsql()->expr($user_query)->getHash();

		// ===== DB.php =========

		$day = strtolower(date("D", strtotime($now)));

		$this->testDebug("====================",'');
		if($accounting_data ===null){
			$this->testDebug('Authentication on ', $now . " [ $day ]");
			$final_row = $this->checkAuthentication($now,$day,$username,$user_data);
			// echo "step 2";
			// die();
		}else{
			if(!is_array($accounting_data)){
				$accounting_data=[$accounting_data,0];
			}
			$this->testDebug('Accounting on ', $now . " [ $day ]",$accounting_data);
			$dl_data = $this->human2byte($accounting_data[0]);
			$ul_data = $this->human2byte($accounting_data[1]);
			$final_row = $this->updateAccountingData($dl_data,$ul_data,$now,$day,$username, $user_data);
		}

		if($human_redable){
			$final_row['data_limit'] = $this->byte2human($final_row['data_limit']);
			$final_row['net_data_limit'] = $this->byte2human($final_row['net_data_limit']);
			$final_row['dl_limit'] = ($final_row['dl_limit'] !== null ) ? $this->byte2human($final_row['dl_limit']):null;
			$final_row['ul_limit'] = ($final_row['ul_limit'] !== null ) ? $this->byte2human($final_row['ul_limit']):null;
			$final_row['data_consumed'] = $this->byte2human($final_row['download_data_consumed'] + $final_row['upload_data_consumed']);
		}

		return ['access'=>$final_row['access'], 'result'=>$final_row];

	}

	// ===== DB.php Start =========
	function getApplicableRow($username,$now,$with_data_limit=false,$less_then_this_id=null){
		
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
		$less_then_id_condition = "";
		if($less_then_this_id) $less_then_id_condition = "AND isp_user_plan_and_topup.id < ".$less_then_this_id;

		$query = "
					SELECT 
						*,
						isp_user_plan_and_topup.id id,
						data_limit + carry_data AS net_data_limit,
						user.last_dl_limit last_dl_limit,
						user.last_ul_limit last_ul_limit
					FROM
						isp_user_plan_and_topup
						JOIN
						isp_user user on isp_user_plan_and_topup.user_id=user.customer_id
					WHERE
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
						`user_id`= (SELECT customer_id from isp_user where radius_username = '$username')
						".
						($with_data_limit? " AND data_limit is not null AND data_limit >0 ":'')
						.
						$less_then_id_condition.
						"
						order by is_topup desc, isp_user_plan_and_topup.id desc
						limit 1
						"
						;

		// echo "step 3 in applicable row ".$query;
		$x = $this->runQuery($query,true);
		if(!count($x)) $x= null;
		$this->testDebug('Querying for '.($with_data_limit?'Data Limit':'Bw Limit').' Row ',null,$query);
		$this->testDebug('Found '.($with_data_limit?'Data Limit':'Bw Limit').' Row ',isset($x['remark'])?$x['remark']:'-',$x);
		return $x;
	}

	function checkAuthentication($now,$day,$username, $user_data){

		$this->testDebug('User',null,$user_data);

		$bw_applicable_row = $this->getApplicableRow($username,$now);

		if(!$bw_applicable_row) {
			// exit in radius
			return ['access' => 0];
		}
		

		$data_limit_row = $bw_applicable_row;
		if(!$bw_applicable_row['net_data_limit']) $data_limit_row = $this->getApplicableRow($username, $now,$with_data_limit=true);
		
		$if_fup='fup_';
		if(($data_limit_row['download_data_consumed'] + $data_limit_row['upload_data_consumed']) < $data_limit_row['net_data_limit']){
			$if_fup='';
		}else{
			if($bw_applicable_row['treat_fup_as_dl_for_last_limit_row']){
				$next_data_limit_row = $this->getApplicableRow($username, $now,null,$data_limit_row['id']);
				
				if( ($next_data_limit_row['download_data_consumed'] + $next_data_limit_row['upload_data_consumed']) > $next_data_limit_row['net_data_limit'] ){
					$data_limit_row['download_limit'] = $next_data_limit_row['fup_download_limit'];
					$data_limit_row['upload_limit'] = $next_data_limit_row['fup_upload_limit'];
					$data_limit_row['remark'] = $next_data_limit_row['remark'];

				}else{
					$data_limit_row['download_limit'] = $bw_applicable_row['fup_download_limit'];
					$data_limit_row['upload_limit'] = $bw_applicable_row['fup_upload_limit'];
					$data_limit_row['remark'] = $next_data_limit_row['remark'];
				}
			}
		}

		// Mark datalimitrow as effective
		$this->runQuery("UPDATE isp_user_plan_and_topup set is_effective=0 where user_id= (SELECT customer_id from isp_user where radius_username = '$username')");
		if($data_limit_row['id']){
			$this->runQuery("UPDATE isp_user_plan_and_topup set is_effective=1 where id=".$data_limit_row['id']);
			$this->testDebug('Setting effective row', $data_limit_row['remark'],$data_limit_row);
		}

		$dl_field = $if_fup.'download_limit';
		$ul_field = $if_fup.'upload_limit';

		// but from which row ??
		// from applicable if values exists
		$dl_limit = $bw_applicable_row[$dl_field];
		$ul_limit = $bw_applicable_row[$ul_field];

		if($dl_limit === null) $dl_limit = $data_limit_row[$dl_field];
		if($ul_limit === null) $ul_limit = $data_limit_row[$ul_field];
		// from data if not 
		// if fup is null or 0 it is a reject authentication command
		// if user dl, ul, accounting not equal to current dl ul then update
		
		$user_update_query = "UPDATE isp_user SET ";
		$speed_value = null;
		if($dl_limit !== $user_data['last_dl_limit'] || $ul_limit !== $user_data['last_ul_limit'] ){
			$speed_value = "last_dl_limit = ".($dl_limit?:'null').",last_ul_limit = ".($ul_limit?:'null');
			$user_update_query .= $speed_value;
		}

		$accounting_value = null;

		if($user_data['last_accounting_dl_ratio'] != $bw_applicable_row['accounting_download_ratio'] || $user_data['last_accounting_ul_ratio'] != $bw_applicable_row['accounting_upload_ratio']){
			$accounting_value = (($speed_value)?", ":" ")."last_accounting_dl_ratio = ".$bw_applicable_row['accounting_download_ratio'].",last_accounting_ul_ratio = ".$bw_applicable_row['accounting_upload_ratio'];
			$user_update_query .= $accounting_value;
		}
		$user_update_query .= " WHERE radius_username = '$username';";


		$coa = false;
		if($speed_value OR $accounting_value ){
			$coa = true;
			$this->testDebug('Updating User',null,$user_update_query);
			$this->runQuery($user_update_query);
		}

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
		$final_row['coa'] = $coa?'1':'0';
		$final_row['access'] = 1;
		
		$access= true;
		if($dl_limit ===null && $ul_limit === null){
			// exit(1);
			$final_row['access'] = 0;
		} 

		return $final_row;

	}

	function updateAccountingData($dl_data,$ul_data,$now,$day,$username, $user_data){

		$this->testDebug('User','in accounting',$user_data);
		
		$consumed_dl_data = ($dl_data*$user_data['last_accounting_dl_ratio']) /100;
		$consumed_ul_data = ($ul_data*$user_data['last_accounting_ul_ratio'])/100;
		// update data query
		$update_query = "
			UPDATE 
				isp_user_plan_and_topup 
			SET 
				download_data_consumed = IFNULL(download_data_consumed,0) + ". ($consumed_dl_data) . ",
				upload_data_consumed = IFNULL(upload_data_consumed,0) + ".($consumed_ul_data) . "
			WHERE 
					is_effective = 1 AND user_id = (SELECT customer_id from isp_user where radius_username = '$username')
				";
		$this->runQuery($update_query);
		$this->testDebug('Updating Accounting Data',['dl'=>$this->byte2human($consumed_dl_data), 'ul'=>$this->byte2human($consumed_ul_data)],$update_query);
		
		$final_row = $this->checkAuthentication($now,$day, $username, $user_data);

		if($final_row['access']==='1' || $final_row['access']===1){
			if($final_row['coa'] === '1' || $final_row['coa'] === 1)
				$final_row['Tmp-Integer-0'] = '1';
			else
				$final_row['Tmp-Integer-0'] = '0';		
		}else{
			$final_row['Tmp-Integer-0'] = '2';
		}

		if($final_row['Tmp-Integer-0']==='1'){
			$final_row['Tmp-String-0'] = $final_row['ul_limit'].'/'.$final_row['dl_limit'];
		}

		return $final_row;
	}

	// ===== DB.php End =========

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
		$radcheck_model = $this->add('xavoc\ispmanager\Model_RadCheck');
		$radcheck_model->addCondition('username',$this['radius_username']);
		$radcheck_model->addCondition('attribute',"Cleartext-Password");
		$radcheck_model->addCondition('op', ":=");
		$radcheck_model->tryLoadAny();
		$radcheck_model['value'] = $this['radius_password'];
		$radcheck_model->save();
	}

	function page_CurrentConditions($page){
		$crud = $page->add('xepan\hr\CRUD');
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

}