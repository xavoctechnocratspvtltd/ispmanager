<?php

namespace xavoc\ispmanager;

class Model_User extends \xepan\commerce\Model_Customer{
	// public $table = "isp_user";
	public $status = ['Active','InActive','Installation','Installed','InDemo','Won'];
	public $actions = [
				'Won'=>['view','assign_for_installation','documents','print_caf','personal_info','communication','edit','delete'],
				'Installation'=>['view','print_caf','personal_info','communication','edit','delete','installed','payment_receive','documents','assign_for_installation'],
				'Installed'=>['view','active_and_change_plan','print_caf','personal_info','assign_for_installation','documents','communication','edit','delete'],
				'Active'=>['view','active_and_change_plan','print_caf','challan','personal_info','communication','edit','delete','AddTopups','CurrentConditions','documents','radius_attributes','deactivate','Reset_Current_Plan_Condition','surrenderRequest','close_session'],
				'InDemo'=>['view','active_and_change_plan','print_caf','challan','personal_info','communication','edit','delete','AddTopups','CurrentConditions','documents','radius_attributes','deactivate','Reset_Current_Plan_Condition','surrenderRequest','close_session'],
				'InActive'=>['view','print_caf','personal_info','communication','edit','delete','active_and_change_plan','documents']
			];

	public $acl_type= "ispmanager_user";
	private $plan_dirty = false;
	private $radius_password_dirty = false;

	public $debug = false;

	function init(){
		parent::init();

		// destroy extra fields
		// $cust_fields = $this->add('xepan\commerce\Model_Customer')->getActualFields();
		
		$this->getElement('created_at')->sortable(true);

		$this->getElement('customer_type')->enum($this->add('xavoc\ispmanager\Model_Config_Mendatory')->getCompanyTypes());

		$destroy_field = ['assign_to_id','scope','is_designer','score','freelancer_type','related_with','related_id','assign_to','created_by_id','source'];
		foreach ($destroy_field as $key => $field) {
			if($this->hasElement($field))
				$this->getElement($field)->system(true);
		}

		$user_j = $this->join('isp_user.customer_id');

		$user_j->hasOne('xavoc\ispmanager\Plan','plan_id','plan_name_with_code')
				->display(['form'=>'autocomplete/Basic']);
		$user_j->hasOne('xavoc\ispmanager\Plan','demo_plan_id')
				->display(['form'=>'autocomplete/Basic']);

		$user_j->addField('customer_id'); // added field why not before
		$user_j->addField('radius_username')->caption('Username')->sortable(true);
		$user_j->addField('radius_password')->caption('Password');
		$user_j->addField('simultaneous_use')->type('Number');
		$user_j->addField('grace_period_in_days')->type('number')->defaultValue(0);
		$user_j->addField('custom_radius_attributes')->type('text')->caption('Custom RADIUS Attributes')->system(true);
		
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
		$user_j->addField('is_active')->type('boolean')->defaultValue(0);

		$user_j->addField('connection_type')->enum($this->add('xavoc\ispmanager\Model_Config_Mendatory')->getConnctionTypes());
		$user_j->addField('surrender_applied_on')->type('date');
		$user_j->addField('is_hotspotuser')->type('boolean')->defaultValue(0);
		$user_j->hasMany('xavoc\ispmanager\UserPlanAndTopup','user_id',null,'PlanConditions');
		$user_j->hasMany('xavoc\ispmanager\Condition','user_id',null,'ActualConditions');
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
		$user_j->addField('installation_assign_at')->type('DateTime');
		$user_j->addField('installed_at')->type('DateTime');
		$user_j->addField('installed_narration')->type('text');

		$this->addExpression('last_login')->set(function($m,$q){
			$act = $m->add('xavoc\ispmanager\Model_RadAcct')
					->addCondition('username',$m->getElement('radius_username'))
					->setOrder('radacctid','desc')
					->setLimit(1);
			return $q->expr('[0]',[$act->fieldQuery('acctstarttime')]);
		})->sortable(true);
		$this->addExpression('last_logout')->set(function($m,$q){
			$act = $m->add('xavoc\ispmanager\Model_RadAcct')
					->addCondition('username',$m->getElement('radius_username'))
					->addCondition('acctstoptime','<>',null)
					->setOrder('radacctid','desc')
					->setLimit(1);
			return $q->expr('[0]',[$act->fieldQuery('acctstoptime')]);
		})->sortable(true);

		$this->addExpression('radius_effective_name',function($m,$q){
			return $q->expr('CONCAT_WS(" :: ",[radius_username],[name],[code],[organization])',
						[
							'radius_username'=>$m->getElement('radius_username'),
							'name'=>$m->getElement('name'),
							'organization'=>$m->getElement('organization'),
							'code'=>$m->getElement('code')
						]
					);
		});

		$this->addExpression('radius_user_created_at')->set(function($m,$q){
			$model = $m->add('xavoc\ispmanager\Model_UserPlanAndTopup');
			$model->addCondition('user_id',$m->getElement('id'));
			$model->setOrder('id','asc');
			$model->setLimit(1);
			return $q->expr('IFNULL([0],0)',[$model->fieldQuery('start_date')]);
		})->caption("Radius User Created At")->type('DateTime');

		$this->add('xepan\base\Controller_AuditLog');
		$this->addHook('beforeSave',$this);
		$this->addHook('afterSave',[$this,'updateNASCredentialHook']);
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

		if($this->loaded() && $this->isDirty('radius_username')){
			$old_user = $this->newInstance()->load($this->id)->get('radius_username');
			$radcheck_model = $this->add('xavoc\ispmanager\Model_RadCheck');
			$radcheck_model->addCondition('username',$old_user);
			foreach ($radcheck_model as $m) {
				$m['username'] = $this['radius_username'];
				$m->saveAndUnload();
			}

			$radreply = $this->add('xavoc\ispmanager\Model_RadReply');
			$radreply->addCondition('username',$old_user);
			foreach ($radreply as $m) {
				$m['username'] = $this['radius_username'];
				$m->saveAndUnload();
			}
			
			// update website user condition only when user status is active or InDemo
			if($this['status'] == "Active" OR $this['status'] == "InDemo"){
				$user = $this->add('xepan\base\Model_User');
				$user->addCondition('scope','WebsiteUser');
				$user->addCondition('username',$old_user);
				$user->tryLoadAny();

				$user['username'] = $this['radius_username'];
				$user->saveAndUnload();
			}
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

		if($this['status'] == "Active" OR $this['status'] == "InDemo"){
			$this['is_active'] = true;
		}else{
			$this['is_active'] = false;
		}
	}

	function updateNASCredentialHook(){
		if($this->radius_password_dirty){
			$this->updateNASCredential();
		}
	}

	function updateUserConditon($expire_all_plan=false,$expire_all_topup=false,$as_grace=true,$on_date=null,$force_plan_end_date=null){
		if(!$this->plan_dirty OR !$this['plan_id']) return;
		
		$this->setPlan($this['plan_id'],$on_date, $remove_old=false,$is_topup=false,$remove_old_topups=false,$expire_all_plan,$expire_all_topup,null,$as_grace,$force_plan_end_date);
	}

	function createInvoice($m,$detail_data=null,$false_condition=false,$master_created_at=null,$force_create=false,$status="Draft"){
		// if(!$false_condition)
		if(!$this['create_invoice'] AND !$force_create) return;
		
		$invoice_data = $this->createQSP($m,$detail_data,'SalesInvoice',null,$master_created_at,$status);
		$channel = $this->add('xepan\base\Model_Contact');
		if($channel->loadLoggedIn('Channel')){
			$asso = $this->add('xavoc\ispmanager\Model_Channel_Association');
			$asso['channel_id'] = $channel->id;
			$asso['invoice_id'] = $invoice_data['master_detail']['id'];
			$asso->save();
		}
		
		// 
		$config = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'lead_lost_region'=>'text',
							'attachment_type'=>'text',
							'invoice_default_status'=>'DropDown'
						],
					'config_key'=>'ISPMANAGER_MISC',
					'application'=>'ispmanager'
			]);
		$config->tryLoadAny();

		if($config['invoice_default_status'] == "Due"){
			$invoice_model = $this->add('xepan\commerce\Model_SalesInvoice')
				->load($invoice_data['master_detail']['id']);
			$invoice_model->approve();
			$invoice_data['master_detail'] = $invoice_model->data;
		}

		return $invoice_data;
	}

	function createQSP($m,$detail_data=[],$qsp_type="SalesInvoice",$plan_id=null,$master_created_at=null,$status="Draft"){
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
		}else{
			$created_at = $this['created_at']?:$this->app->now;
		}
		
		$qsp_config = $this->add('xepan\commerce\Model_Config_QSPConfig');
		$qsp_config->tryLoadAny();

		$serial = "";
		if($qsp_type == "SalesOrder"){
			$serial = $qsp_config['sale_order_serial'];
		}
		if($qsp_type == "SalesInvoice"){
			$serial = $qsp_config['sale_invoice_serial'];
		}
		if($qsp_type == "Quotation"){
			$serial = $qsp_config['quotation_serial'];
		}
		
		$master_data['serial'] = $serial;
		$master_data['qsp_no'] = $this->add('xepan\commerce\Model_'.$qsp_type)->newNumber();
		$master_data['contact_id'] = $this->id;
		$master_data['branch_id'] = $this['branch_id'];
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
		$master_data['tnc_id'] = $this->add('xepan\commerce\Model_TNC')->addCondition('is_default_for_sale_invoice',true)->tryLoadAny()->id;
		$master_data['nominal_id'] = $this->add('xepan\accounts\Model_Ledger')->load('Sales Account')->get('id');
		$master_data['status'] = $status;
		if(!count($detail_data)){
			$detail_data = [];
			if($plan_id > 0)
				$plan_model = $this->add('xavoc\ispmanager\Model_Plan')->load($plan_id);
			else
				$plan_model = $this->add('xavoc\ispmanager\Model_Plan')->load($this['plan_id']);

			$next_end_date = date("Y-m-d H:i:s", strtotime("-1 Day", strtotime("+".$plan_model['plan_validity_value']." ".$plan_model['qty_unit'],strtotime($created_at))));

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
						'treat_sale_price_as_amount'=>$plan_model['treat_sale_price_as_amount'],
						'discount'=>0,
						'narration'=>'Start Date: '.date('Y-m-d',strtotime($created_at))." End Date: ".date('Y-m-d', strtotime($next_end_date))
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
		
		$t = $qsp_master->createQSP($master_data,$detail_data,$qsp_type);
		return $t;
	}

	function getProDataAmount(){
		if(!$this->loaded()) throw new \Exception("radius user must loaded");
		
		return 0;
	}

	function addTopup($topup_id,$date=null,$remove_old_topups=false){
		$this->setPlan($topup_id,$date,false,true,$remove_old_topups);
	}

	function surrenderRefundValue($in_days=true,$refund_tax_value=false){

		$refund_value = [
				'qsp_detail'=>null,
				'plan'=>null,
				'effective_end_date'=>0,
				'effective_start_date'=>0,
				'actual_days'=>0,
				'refund_days'=>0,
				'refund_value'=>0,
				'invoice_based_refund_amount'=>0,
				'total_plan_data'=>0,
				'total_data_consumed'=>0,
				'data_based_refund_amount'=>0,
			];

		$qsp_detail_model = $this->add('xepan\commerce\Model_QSP_Detail')
					->addCondition('item_id',$this['plan_id'])
					->addCondition('customer_id',$this->id)
					->setOrder('created_at','desc')
					->tryLoadAny()
					;
		if(!$qsp_detail_model->loaded()){
			return $refund_value;
		} 
		
		$effective_start_date = $last_invoice_date = $qsp_detail_model['created_at'];
		$plan_model = $this->add('xavoc\ispmanager\Model_Plan')->load($this['plan_id']);

		$effective_end_date = date("Y-m-d", strtotime("+".$plan_model['plan_validity_value']." ".$plan_model['qty_unit'],strtotime($effective_start_date)));

		if($plan_model['free_tenure'] AND $plan_model['free_tenure_unit']){
			$effective_end_date = date("Y-m-d", strtotime("-".$plan_model['free_tenure']." ".$plan_model['free_tenure_unit'],strtotime($effective_end_date)));
		}

		$effective_start_time = strtotime($effective_start_date);
		$effective_end_time = strtotime($effective_end_date);
		$on_time = strtotime($this->app->now);

		$actual_differance = $this->app->my_date_diff($effective_end_date,$effective_start_date);

		if($on_time > $effective_end_time) return $refund_value;

		$date_diff = $this->app->my_date_diff($this->app->now,$effective_end_date);
		$amount = $refund_tax_value?$qsp_detail_model['total_amount']:$qsp_detail_model['amount_excluding_tax'];

		if($in_days){
			$actual_days = $actual_differance['days_total'];
			$refund_days = $date_diff['days_total'];
			$refund_value =  $amount*($refund_days/$actual_days);
		}

		$round_standard_name = $this->add('xepan\base\Model_ConfigJsonModel',
				[
					'fields'=>[
								'round_amount_standard'=>'DropDown'
								],
						'config_key'=>'COMMERCE_TAX_AND_ROUND_AMOUNT_CONFIG',
						'application'=>'commerce'
				]);
		$round_standard_name->tryLoadAny();
		$round_standard = $round_standard_name['round_amount_standard'];

		switch ($round_standard) {
			case 'Standard':
				$refund_value = round($refund_value);
				break;
			case 'Up':
				$refund_value = ceil($refund_value);
				break;
			case 'Down':
				$refund_value = floor($refund_value);
				break;
		}


		// data based refund amount
		$rad_model = $this->add('xavoc\ispmanager\Model_RadAcctData');
		$rad_model->addCondition('username',$this['radius_username']);
		$rad_model->addCondition('acctstarttime','>=',$effective_start_date);
		$rad_model->addCondition('acctupdatetime','<',$this->app->nextDate($this->app->now));
		$rad_model->setOrder('radacctid','desc');
		$rad_model->tryLoadAny();

		$total_plan_data = $plan_model->ref('conditions')->sum('data_limit')->getOne();
		$total_consumed_data = $rad_model['total_download'] + $rad_model['total_upload'];
		$unused_data = $total_plan_data-$total_consumed_data;
		$data_based_refund_amount = ($amount/$total_plan_data)*$unused_data;
		// end of data based refund amount
		$refund_value = [
			'plan_name'=>$plan_model['name']." - ".$plan_model['sku'],
			'invoice_number'=>$qsp_detail_model['qsp_master'],
			'invoice_amount'=>$amount,
			'effective_end_date'=>$effective_end_date,
			'effective_start_date'=>$effective_start_date,
			'actual_days'=>$actual_days,
			'refund_days'=>$refund_days,
			'invoice_based_refund_amount'=>$refund_value,
			'total_plan_data'=>$total_plan_data,
			'total_data_consumed'=>$total_consumed_data,
			'data_based_refund_amount'=>$data_based_refund_amount,
		];
		

		return $refund_value;
	}

	function page_surrenderRequest($page){
		$sr_model = $this->add('xavoc\ispmanager\Model_SurrenderRequest');
		$sr_model->addCondition('contact_id',$this->id);
		$crud = $page->add('xepan\hr\CRUD',['status_color'=>$sr_model->status_color]);
		$crud->setModel($sr_model,['contact_id','assign_to_id','created_at','device_collection_availibility','narration'],['contact','assign_to','created_at','device_collection_availibility','narration','status']);
		$crud->grid->addFormatter('contact','Wrap');
		$crud->grid->addFormatter('narration','Wrap');
		$crud->grid->removeAttachment();
		$crud->grid->removeColumn('status');
	}

	function page_force_surrender($page){
		$this->page_surrenderPlan($page,$force_surrender=true);
	}


	function getRefundableSecurityDeposit($nominals_ids_arr=null){

		if(!$nominals_ids_arr){
			$config = $this->add('xepan\base\Model_ConfigJsonModel',
				[
					'fields'=>[
								'refundable_nominal_accounts'=>'xepan\base\Multiselect',
							],
						'config_key'=>'ISPMANAGER_Refundable_Nominal_Accounts',
						'application'=>'ispmanager'
				]);
			$config->tryLoadAny();
			$nominals_ids_arr = explode(",",$config['refundable_nominal_accounts']);
		}

		// $nominals = $this->add('xepan\commerce\Model_Ledger')->addCondition('id',$nominals_ids_arr);
		$transaction_row = $this->add('xepan\accounts\Model_TransactionRow');
		$transaction_row->addCondition('ledger_id',$nominals_ids_arr);
		$tr_j = $transaction_row->join('account_transaction','transaction_id');
		$tr_j->addField('related_id');
		$tr_j->addField('related_type');

		$inv_j = $tr_j->join('qsp_master.document_id','related_id');
		$inv_j->addField('contact_id');

		$transaction_row->addCondition('contact_id',$this->id);
		$transaction_row->addCondition('related_type','xepan\commerce\Model_SalesInvoice');
		$balance = [];
		foreach ($transaction_row as $trr) {
			if(!isset($balance[$trr['ledger_id']])) {
				$balance[$trr['ledger_id']] = ['amountCr'=>0,'amountDr'=>0,'name'=>$trr['ledger']];
			}
			$filled_side = $trr['amountCr']?'amountCr':'amountDr';
			$balance[$trr['ledger_id']][$filled_side] += $trr[$filled_side];
		}

		return $balance;

	}

	function getIssuedDevices(){

		$m = $this->add('xepan\commerce\Model_Store_TransactionAbstract');
		$m->addCondition('type',"Issue");
		$m->addCondition('to_warehouse_id',$this->id);
		$tran_ids = array_column($m->getRows(),'id');
		$tran_ids = array_combine($tran_ids,$tran_ids);

		$tr = $this->add('xepan\commerce\Model_Store_TransactionRow');
		$tr->addCondition('store_transaction_id',$tran_ids);
		
		return $tr;

	}

	function adjustRefundaleSecurityDeposit($amount){

	}

	/**
		arr =[
			device_id/type/serial=>returned/damage
		]
	*/
	function submitIssuedDevices($arr=[]){

	}


	// ON SURRENDER:
	// Ask surrenderable or not
	// If surrenderable, Ask 1 month notice period is served or not
	// If yes proceed for surrender
	// If no ask customer to serve notice,
	// On yes:
	// Security deposit is available or not
	// If available ask for any other charge is required to be deducted
	// Accountant will raise a request to process surrender to technical team.
	// Accountant will ask customer about availibility of time & Bank Accounts details.
	// When Tech team reach customer end they will acknowledge about device in good condition & accountant will make neft to customer.

	function page_surrenderPlan($page,$force_surrender = false){

		$qsp_detail_model = $this->add('xepan\commerce\Model_QSP_Detail')
					->addCondition('item_id',$this['plan_id'])
					->addCondition('customer_id',$this->id)
					->setOrder('created_at','desc')
					->tryLoadAny()
					;
		if(!$qsp_detail_model->loaded()){
			$page->add('View')->set("No last invoice found, cannot proceed please deactivate user and adjust amount manually")->addClass('alert alert-danger');
			return;
		} 

		if(!$force_surrender){
			$plan = $this->add('xavoc\ispmanager\Model_Plan')->load($this['plan_id']);
			if(!$plan['is_surrenderable']){
				$page->add('View')->set("Plan is not surrenderable")->addClass('alert alert-danger');
				return;
			}
		}

		$refund_security_deposite = [];
		$refund_security_deposite = $this->getRefundableSecurityDeposit();


		// ===== all things will change now, devices recived in separate model and 
		// ===== here only show that json, received damaged or what
		// ===== and show amount to be adjusted in user account by any transaction or jv


		// update surrender applied on date
		$view = $page->add('View');

		if(!$this['surrender_applied_on']){
			$view->add('View_Error')->set('Surrender 1 month notice period is not surved');
		}else{
			$date_diff = $this->app->my_date_diff($this->app->today,$this['surrender_applied_on']);
			if(!$date_diff['months'])
				$view->add('View')->setHtml('<h3>Surrender 1 month notice period is not surved, Total Days Served: '.$date_diff['days']." Applied on: ".$this['surrender_applied_on']."</h3>")->addClass('bg bg-warning');
		}

		$form = $view->add('Form');
		$form->add('xepan\base\Controller_FLC')
		->showLables(true)
		->makePanelsCoppalsible(true)
		->layout([
				'none~&nbsp;'=>'Surrender Applied On~c1~1',
				'surrender_applied_on'=>'c2~6',
				'FormButtons~&nbsp;'=>'c3~4'
			]);
		$form->addField('DatePicker','surrender_applied_on')->set($this['surrender_applied_on']);
		$form->addSubmit('Update Surrender Apply Date')->addClass('btn btn-info');
		if($form->isSubmitted()){
			$this['surrender_applied_on'] = $form['surrender_applied_on'];
			$this->save();
			$view->js()->reload();
		}

		// check one month is served or not
		
		$refund_tax_value = false;
		$in_days = true;
		$refund_value = $this->surrenderRefundValue($in_days,$refund_tax_value);
		
		$issued_items = $this->getIssuedDevices();
		// $this->app->print_r($issued_items->getRows());

		// $page->add('View')->addClass('alert alert-info')
		// 		->set('Refund Amount: '.$this->app->epan->default_currency['name']);
		$form = $page->add('Form');
		$form->addSubmit('Surrender Now')->addClass('btn btn-primary');
		if($form->isSubmitted()){
			$this->surrenderPlan($refund_value,true,true,$qsp_detail_model['qsp_master_id']);
			return $this->app->page_action_result = $this->app->js(null,$page->js()->univ()->closeDialog())->univ()->successMessage('User Plan Deactivated');
		}

	}

	function surrenderPlan($deactivate_user=true,$in_days=true,$refund_tax_value=false,$invoice_id=null){

		if($refund_value = $this->surrenderRefundValue($in_days,$refund_tax_value)){
			// Do accounts entry for this customer
			$entry_template =$this->add('xepan\accounts\Model_EntryTemplate')->loadBy('unique_trnasaction_template_code','jv');
			$transaction = $entry_template->ref('xepan\accounts\EntryTemplateTransaction')->tryLoadAny(); 

			// $new_transaction->createNewTransaction($transaction['type'],null,date('Y-m-d',strtotime($transaction['transaction_date'])),$transaction['narration'],$transaction['currency'],$transaction['exchange_rate'],null,null,null,$transaction['entry_template_id']);
			$entry_template->executeSave([
				$transaction->id => [
					// 'entry_template_transaction_id'=>$transaction->id,
					'entry_template_id'=>$entry_template->id,
					// 'name'=>$transaction['name'],
					'type'=>$transaction['type'],
					'transaction_date'=>$this->app->now,
					'narration'=> "Plan Surrender Payment Refund",
					'currency'=>$this->app->epan->default_currency->id,
					'related_id'=>$invoice_id?:0,
					'related_type'=>'xepan\commerce\Model_SalesInvoice',
					'exchange_rate'=>1,
					'rows'=>[
								[
									'data-code'=>'',
									'currency'=>$this->app->epan->default_currency->id,
									'exchange_rate'=>1,
									'data-side'=>'DR',
									'data-ledger'=> $this->add('xepan\accounts\Model_Ledger')->tryLoadBy('name','Sales Account')->get('id'),
									'data-amount'=> $refund_value
								],
								[
									'data-code'=>'',
									'currency'=>$this->app->epan->default_currency->id,
									'exchange_rate'=>1,
									'data-side'=>'CR',
									'data-ledger'=> $this->ledger()->get('id'),
									'data-amount'=>$refund_value
								]
							]

				]
			]);
		}

		$this->add('xepan\communication\Model_Communication_Comment')
			->createNew($this->app->employee,$this,"User Plan Surrender","Plan Surrender",$on_date=$this->app->now);

		if($deactivate_user){
			$this->deactivate();
		}

		$query = "UPDATE isp_user_plan_and_topup SET is_expired = '1' WHERE user_id = ".$this->id;
		$this->app->db->dsql()->expr($query)->execute();
		return true;
	}

	// how set plan works
	// if same plan applied AND last cindition is same plan condition then it only update the start and end date on same alst condition and set is_expired false
	// if last user last condition is not the plan conditon then it create a new entry and work accordingly

	function setPlan($plan, $on_date=null, $remove_old=false,$is_topup=false,$remove_old_topups=false,$expire_all_plan=false,$expire_all_topup=false,$work_on_pro_data=true,$as_grace = true,$force_plan_end_date=null,$force_set_plan=false,$set_reset_date=true){
		
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

		$is_same_plan_continued = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup')
									->addCondition('user_id',$this->id)
									->addCondition('is_topup',false)
									->setOrder('id','desc')
									->setLimit(1)
									->tryLoadAny()->get('plan_id') == $plan_model->id;

		// as per logic.jade if force set plan then update it's start date in a new condition
		if($force_set_plan)
			$is_same_plan_continued = 0;

		// expire 
		if($expire_all_plan){
			$old_p = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
			$old_p->addCondition('user_id',$this->id);
			$old_p->addCondition('is_topup',false);
			$old_p->_dsql()->set('is_expired',1)->update();
		}

		if($expire_all_topup){
			$old_p = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
			$old_p->addCondition('user_id',$this->id);
			$old_p->addCondition('is_topup',true);
			$old_p->_dsql()->set('is_expired',1)->update();
		}
		

		foreach ($condition_model as $key => $condition) {
			
			$fields = $condition->getActualFields();
			$unset_field =  ['id','plan_id','plan'];
			$fields = array_diff($fields,$unset_field);

			$u_p = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
			$u_p->addCondition('user_id',$this->id)
				->addCondition('plan_id',$plan_model->id)
				->addCondition('condition_id',$condition['id'])
				// ->addCondition([['is_expired',false],['is_expired',null]])
				;
			$u_p->setOrder('id','desc');
			$u_p->setLimit(1);
			if($is_same_plan_continued){
				$u_p->tryLoadAny();
			}

			if(!$u_p->loaded()){
				$u_p['is_effective'] = 0;
				$u_p['start_date'] = $on_date;															
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
			if($this['is_invoice_date_first_to_first'] && $work_on_pro_data){
				$end_date = date("Y-m-t H:i:s", strtotime($on_date));
			}

			if($condition['data_reset_value'] AND $set_reset_date){

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

				$u_p['session_download_data_consumed_on_reset'] = $u_p['session_download_data_consumed'];
				$u_p['session_upload_data_consumed_on_reset'] = $u_p['session_upload_data_consumed'];
				
			}else{
				$reset_date = null;
			}

			if(!$set_reset_date) $reset_date = $u_p['reset_date'];

			// factor based on implemention
			if($force_plan_end_date){
				$end_date = $force_plan_end_date;
			}

			$u_p['end_date'] = $end_date;

			if($as_grace AND !$force_plan_end_date){
				$u_p['expire_date'] = $u_p['is_topup']? $on_date : date("Y-m-d H:i:s", strtotime("+".($this['grace_period_in_days']?:0)." days",strtotime($on_date)));
			}else{
				$u_p['expire_date'] = $u_p['is_topup']? $end_date : date("Y-m-d H:i:s", strtotime("+".($this['grace_period_in_days']?:0)." days",strtotime($end_date)));
			}
			$u_p['is_recurring'] = $plan_model['is_renewable'];
			$u_p['reset_date'] = $reset_date;
			$u_p['is_expired'] = false;
			
			$u_p['data_limit_row'] = null; //id condition has data_limit then set empty else previous data row limit id;
			
			// pro data update data_limit
			if( $work_on_pro_data && $condition['is_pro_data_affected'] && $this['is_invoice_date_first_to_first'] && in_array($this['include_pro_data_basis'], ['data_only','invoice_and_data_both']) && $reset_date){
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
	
		$title = "New Plan (".$plan_model['name']." ".$plan_model['sku'].") Implemented by employee ".$this->app->employee['name'];
		if($force_set_plan){
			$title .= " reset plan forcely on date ". @$this->app->reset_same_plan_again_on_date;
		}

		$this->add('xepan\communication\Model_Communication_Comment')
			->createNew($this->app->employee,$this,$title,"Plan (".$plan_model['name']." ".$plan_model['sku'].") Implemented by employee ".$this->app->employee['name'],$on_date=$this->app->now);

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

	function page_documents($page){

		$attachment = $this->add('xavoc\ispmanager\Model_Attachment');
		$attachment->addCondition('contact_id',$this->id);
		$crud = $page->add('CRUD');
		$crud->setModel($attachment,['title','file_id','description'],['title','file','description']);
		$crud->grid->addFormatter('file','image');

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

		$topup = $this->add('xavoc\ispmanager\Model_TopUp');
		$topup->addCondition('status','Published');

		$form = $page->add('Form');
		$form->addField('DropDown','topup')
			->validate('required')
			->setEmptyText('Please Select Topup')
			->setModel($topup);
		$form->addSubmit('Add TopUp');

		$crud = $page->add('CRUD',['allow_add'=>false]);
		// if($crud->isEditing()){
		// 	$form = $crud->form;
		// 	$form->add('xepan\base\Controller_FLC')
		// 		->addContentSpot()
		// 		->layout([
		// 				'remark~Row Name'=>'About Plan~c1~3',
		// 				'data_limit'=>'c2~3~Data limit in Human Readable Formate 20gb, 1tb, 100mb',
		// 				'time_limit'=>'c3~3~Time limit in minutes',
		// 				'is_data_carry_forward~Data Carry Forward'=>'c4~3',
		// 				// 'plan_id~Topup'=>'c5~3',

		// 				// 'download_limit'=>'Topup DL/UL Limit~c1~3~Limit per second',
		// 				// 'upload_limit'=>'c11~3~Limit per second',
		// 				// 'fup_download_limit'=>'c12~3~Limit per second',
		// 				// 'fup_upload_limit'=>'c13~3~Limit per second',
		// 				// 'accounting_download_ratio'=>'c2~6~Ratio in %',
		// 				// 'accounting_upload_ratio'=>'c21~6~Ratio in %',

		// 				// 'download_data_consumed'=>'Consumed TOPUP Data~c31~4',
		// 				// 'upload_data_consumed'=>'c32~4',
		// 				// 'data_limit_row'=>'c33~4',
		// 				// 'carry_data'=>'c34~4',

		// 				// 'start_date'=>'Date & Time~c1~3',
		// 				// 'start_time'=>'c2~3',
		// 				// 'end_date'=>'c3~3',
		// 				// 'end_time'=>'c4~3',
		// 				// 'expire_date'=>'c5~3',
		// 				// 'reset_date'=>'c6~3',
		// 				// 'is_expired~'=>'c7~2',
		// 				// 'is_recurring~'=>'c8~2',
		// 				// 'is_effective~'=>'c9~2',

		// 				// 'sun'=>'Week~c1~1',
		// 				// 'mon'=>'c2~1',
		// 				// 'tue'=>'c3~1',
		// 				// 'wed'=>'c4~1',
		// 				// 'thu'=>'c5~1',
		// 				// 'fri'=>'c6~1',
		// 				// 'sat'=>'c7~1',
		// 				// 'd01'=>'Days~c1~1',
		// 				// 'd02'=>'c2~1',
		// 				// 'd03'=>'c3~1',
		// 				// 'd04'=>'c4~1',
		// 				// 'd05'=>'c5~1',
		// 				// 'd06'=>'c6~1',
		// 				// 'd07'=>'c7~1',
		// 				// 'd08'=>'c8~1',
		// 				// 'd09'=>'c9~1',
		// 				// 'd10'=>'c10~1',
		// 				// 'd11'=>'c11~1',
		// 				// 'd12'=>'c12~1',
		// 				// 'd13'=>'c13~1',
		// 				// 'd14'=>'c14~1',
		// 				// 'd15'=>'c15~1',
		// 				// 'd16'=>'c16~1',
		// 				// 'd17'=>'c17~1',
		// 				// 'd18'=>'c18~1',
		// 				// 'd19'=>'c19~1',
		// 				// 'd20'=>'c20~1',
		// 				// 'd21'=>'c21~1',
		// 				// 'd22'=>'c22~1',
		// 				// 'd23'=>'c23~1',
		// 				// 'd24'=>'c24~1',
		// 				// 'd25'=>'c25~1',
		// 				// 'd26'=>'c26~1',
		// 				// 'd27'=>'c27~1',
		// 				// 'd28'=>'c28~1',
		// 				// 'd29'=>'c29~1',
		// 				// 'd30'=>'c30~1',
		// 				// 'd31'=>'c31~1',
		// 				// 'data_reset_value'=>'Reset Data~c1~6',
		// 				// 'data_reset_mode'=>'c2~6',
		// 				// 'burst_dl_limit'=>'Burst~c1~3~limit per second',
		// 				// 'burst_ul_limit'=>'c11~3~limit per second',
		// 				// 'burst_threshold_dl_limit'=>'c12~3~limit per second',
		// 				// 'burst_threshold_ul_limit'=>'c13~3~limit per second',
		// 				// 'burst_dl_time'=>'c2~3~time in second',
		// 				// 'burst_ul_time'=>'c21~3~time in second',
		// 				// 'priority'=>'c22~3',
		// 				// 'treat_fup_as_dl_for_last_limit_row'=>'MISC~c1~6',
		// 				// 'is_pro_data_affected'=>'c2~6',
		// 				// 'carry_data'=>'c3~4'
		// 		]);
		// }

		if($form->isSubmitted()){
			$this->addTopup($form['topup']);
			return $this->app->page_action_result = $this->app->js(true,$crud->js()->reload())->univ()->successMessage('Topup Added Successfully');
		}

		$field_to_show =[
						'user_id',
						'remark',
						'data_limit',
						'time_limit',
						'is_data_carry_forward',
						'plan_id',
						'download_limit',
						'upload_limit',
						'fup_download_limit',
						'fup_upload_limit',
						'accounting_download_ratio',
						'accounting_upload_ratio',
						'start_time',
						'end_time',
						'sun',
						'mon',
						'tue',
						'wed',
						'thu',
						'fri',
						'sat',
						'd01',
						'd02',
						'd03',
						'd04',
						'd05',
						'd06',
						'd07',
						'd08',
						'd09',
						'd10',
						'd11',
						'd12',
						'd13',
						'd14',
						'd15',
						'd16',
						'd17',
						'd18',
						'd19',
						'd20',
						'd21',
						'd22',
						'd23',
						'd24',
						'd25',
						'd26',
						'd27',
						'd28',
						'd29',
						'd30',
						'd31',
						'data_reset_value',
						'data_reset_mode',
						'burst_dl_limit',
						'burst_ul_limit',
						'burst_threshold_dl_limit',
						'burst_threshold_ul_limit',
						'burst_dl_time',
						'burst_ul_time',
						'priority',
						'treat_fup_as_dl_for_last_limit_row',
						'is_pro_data_affected',
						'download_data_consumed',
						'upload_data_consumed',
						'carry_data',
						'data_limit_row',
						'start_date',
						'end_date',
						'expire_date',
						'reset_date',
						'is_expired',
						'is_recurring',
						'is_effective',
						'condition_id'
					];
		$model = $page->add('xavoc\ispmanager\Model_UserPlanAndTopup');
		$model->addCondition('is_topup',true)
			->addCondition('user_id',$this->id);
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
		$crud->addClass('current-condition');
		$crud->js('reload')->reload();

		$crud->grid->add('View',null,'grid_heading_left')->setHtml("User: <b>".$this['radius_effective_name']."</b><br/>Current Plan: <b>".$this['plan']."</b>")->addClass('alert alert-info');
		
		if($crud->isEditing()){
			$form = $crud->form;
			$form->add('xepan\base\Controller_FLC')
				->addContentSpot()
				// ->makePanelsCoppalsible()
				->layout([
						'plan_id'=>'About Plan~c1~3',
						// 'condition_id'=>'c11~3',
						'remark'=>'c2~2',
						'is_topup'=>'c3~2',
						'data_limit'=>'c4~3',
						'carry_data'=>'c5~2',
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
						'explanation'=>'c1~6',
						'is_pro_data_affected'=>'c2~6',
						'burst_dl_limit'=>'Burst~c1~3~limit per second',
						'burst_ul_limit'=>'c11~3~limit per second',
						'burst_threshold_dl_limit'=>'c12~3~limit per second',
						'burst_threshold_ul_limit'=>'c13~3~limit per second',
						'burst_dl_time'=>'c2~3~time in second',
						'burst_ul_time'=>'c21~3~time in second',
						'priority'=>'c22~6',
				]);
			
			$b = $form->layout->add('Button',null,'explanation')
				->set('explanation');
			$b->add('VirtualPage')
			->bindEvent('Explanation of treat fup as dl for last limit row','click')
			->set([$this,"explanation"]);

		}
		$model = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
		$model->addCondition('user_id',$this->id);
		$crud->setModel($model);
		$model->setOrder(['id desc','is_expired desc']);
		// if($crud->isEditing()){
		// 	$form = $crud->form;
		// 	$form->getElement('start_time')
		// 		->setOption('showMeridian',false)
		// 		->setOption('defaultTime',0)
		// 		->setOption('minuteStep',1)
		// 		->setOption('showSeconds',true)
		// 		;
		// 	$form->getElement('end_time')
		// 		->setOption('showMeridian',false)
		// 		->setOption('defaultTime',0)
		// 		->setOption('minuteStep',1)
		// 		->setOption('showSeconds',true)
		// 		;
		// }

		$crud->grid->addColumn('validity');
		$crud->grid->addColumn('detail');
		$crud->grid->addColumn('week_days');
		$crud->grid->addColumn('off_dates');
		$crud->grid->addColumn('burst_detail');
		
		$crud->grid->plan_changed = false;
		$crud->grid->current_plan_id = 0;
		$crud->grid->edit_html = "";

		$crud->grid->addHook('formatRow',function($g){
			// data detail
			$speed = "UP/DL Limit: ".$g->model['upload_limit']."/".$g->model['download_limit']."<br/>";
			$speed .= "FUP UP/DL Limit: ".$g->model['fup_upload_limit']."/".$g->model['fup_download_limit']."<br/>";
			$speed .= "Accounting UP/DL Limit: ".$g->model['accounting_upload_ratio']."%/".$g->model['accounting_download_ratio']."%<br/>";
			$speed .= "start/end time: ".$g->model['start_time']."/".$g->model['end_time']."<br/>";
			if($g->model['treat_fup_as_dl_for_last_limit_row'])
				$speed .= "<strong style='color:red;'>FUP as DL for last limit row</strong><br/>";

			$speed .= "Time Limit: ".($g->model['time_limit']>0?($g->model['time_limit']." minutes"):"");
			$g->current_row_html['detail'] = $speed;
			
			$week_days = '';
			foreach (['sun','mon','tue','wed','thu','fri','sat'] as $name) {
				if($g->model[$name])
  					$week_days .= "<span style='color:green;'>".strtoupper(substr($name,0,1))."&nbsp;</span>";
  				else
  					$week_days .= "<span style='color:red;'>".strtoupper(substr($name,0,1))."&nbsp;</span>";
			}
			$g->current_row_html['week_days'] = $week_days;
			
			$week_days .= '</div>';

			$off_dates = "";
			foreach (['d01','d02','d03','d04','d05','d06','d07','d08','d09','d10','d11','d12','d13','d14','d15','d16','d17','d18','d19','d20','d21','d22','d23','d24','d25','d26','d27','d28','d29','d30','d31'] as $name) {
				if(!$g->model[$name])
					$off_dates .= trim($name,'d').",";
			}
			$g->current_row_html['off_dates'] = trim($off_dates,',');
			
			// burts detail
			$bt = "UL\DL Limit: ".$g->model['burst_ul_limit']."/".$g->model['burst_dl_limit']."<br/>";
			$bt .= "UL\DL Time: ".$g->model['burst_ul_time']."/".$g->model['burst_dl_time']."<br/>";
			$bt .= "Threshold UL\DL Time: ".$g->model['burst_threshold_ul_limit']."/".$g->model['burst_threshold_dl_limit']."<br/>";
			$bt .= "Priority: ".$g->model['priority'];
			$g->current_row_html['burst_detail'] = $bt;

			$detail = "Carry Data: ".$g->model['carry_data']."<br/>Condition Data: ".$g->model['data_limit']."<br/>Net Data: ".$g->model['net_data_limit']."<br/>"."Reset Every: ".($g->model['data_reset_value']." ".$g->model['data_reset_mode'])."<br/> Carried: ".$g->model['is_data_carry_forward']."<br/>";
			if(!$g->model['is_pro_data_affected'])
				$detail .= "<strong style='color:red;'>Pro Data Not Affected</strong>";
			else
				$detail .= "Pro Data Affected";

			$g->current_row_html['data_limit'] = $detail;

			// validity
			$g->current_row_html['validity'] = "Start Date: ".$g->model['start_date']."<br/>End Date: ".$g->model['end_date']."<br/>Expire Date: ".$g->model['expire_date']."<br/>Next Reset Date: ".$g->model['reset_date'];
			$g->current_row_html['remark'] = "<strong style='font-size:14px;'>".$g->model['plan']."</strong><br/>".$g->model['remark'].($g->model['is_topup']?"<strong style='color:red;'>TopUp</strong>":"").($g->model['is_expired']?('<br/><div class="label label-danger">Expired</div>'):"");
			// $g->current_row_html['data_consumed'] = $g->model['data_consumed'];

			if($g->model['is_effective']){
				$g->setTDParam('remark','class',"green-bg");
			}else
				$g->setTDParam('remark','class'," ");

			// if(!$g->current_plan_id){
			// 	$g->current_plan_id = $g->model['plan_id'];
			// 	$g->edit_html .= "Current Plan id = ".$g->current_plan_id." name = ".$g->model['plan'];
			// } 

			// if(!$g->plan_changed AND $g->model['plan_id'] != $g->current_plan_id){
			// 	$g->plan_changed = true;
			// 	$g->edit_html .= "Plan Changed = ".$g->plan_changed;
			// }

			// if($g->plan_changed){
			// 	$g->current_row_html['edit'] = ' ';
			// }

		});
		$removeColumn_list = [
					'user_id','user','condition','plan','upload_limit','download_limit','fup_download_limit','fup_upload_limit','accounting_upload_ratio','accounting_download_ratio',
					'sun','mon','tue','wed','thu','fri','sat','d01','d02','d03','d04','d05','d06','d07','d08','d09','d10','d11','d12','d13','d14','d15','d16','d17','d18','d19','d20','d21','d22','d23','d24','d25','d26','d27','d28','d29','d30','d31',
					'start_time','end_time','net_data_limit','carry_data',
					'data_reset_mode','data_reset_value','is_data_carry_forward',
					'burst_ul_limit','burst_dl_limit','burst_ul_time','burst_dl_time','burst_threshold_ul_limit','burst_threshold_dl_limit','priority',
					'treat_fup_as_dl_for_last_limit_row','is_pro_data_affected','action',
					'start_date','end_date','expire_date','is_topup','reset_date',
					'download_data_consumed','upload_data_consumed','time_limit','data_limit_row','duplicated_from_record_id',
					'is_recurring','is_effective','is_expired'
				];
		foreach ($removeColumn_list as $field) {
			$crud->grid->removeColumn($field);
		}		
		$crud->grid->removeAttachment();

		$crud->grid->add('VirtualPage')
			->addColumn('reset_data')
			->set(function($page){

				$id = $_GET[$page->short_name.'_id'];
				$model = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
				$model->load($id);

				$page->add('View_Warning')->set('Are you sure you want to continue ?');
				
				$reset_date = date("Y-m-d H:i:s", strtotime("+".$model['data_reset_value']." ".$model['data_reset_mode'],strtotime($model['reset_date'])));
				$current_reset_date = "Current Reset Date: <b>".$model['reset_date']."</b><br/> After Including reset date it become: <b>".$reset_date."</b>";

				$form = $page->add('Form');
				$form->addField('CheckBox','include_reset_date_also')->set(0);
				$form->add('View_Info')->setHtml($current_reset_date);
				$form->addSubmit('Reset Data')->addClass('btn btn-danger');
				if($form->isSubmitted()){
					$model->resetData($form['include_reset_date_also']);

					$js = [$page->js()->_selector('.current-condition')->trigger('reload'),$page->js()->univ()->closeDialog()];
					$form->js(null,$js)->univ()->successMessage('Data Reset Successfully')->execute();
				}

			});
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
		$this->app->skip_audit_log = true;
		
		// get all plan list
		$plan_list = [];
		// if($this->app->recall('isp_user_import_plan',false) == false){
			foreach ($this->add('xavoc\ispmanager\Model_Plan')->getRows() as $key => $plan) {
				$plan_list[strtolower(trim($plan['sku']))] = $plan['id'];
			}
			// $this->app->memorize('isp_user_import_plan',$plan_list);
		// }
		// $plan_list = $this->app->recall('isp_user_import_plan');

		
		// get all country list
		$country_list = [];
		if($this->app->recall('isp_user_import_country',false) == false){
			foreach($this->add('xepan\base\Model_Country') as $key => $country){
				$country_list[strtolower(trim($country['name']))] = $country['id'];
			}
			$this->app->memorize('isp_user_import_country',$country_list);
		}
		$country_list = $this->app->recall('isp_user_import_country');

		$state_list = [];
		if($this->app->recall('isp_user_import_state',false) == false){
			$state_model = $this->add('xepan\base\Model_State');
			foreach ($state_model as $key => $state) {
				$state_list[strtolower(trim($state['name']))] = $state['id'];
			}

			$this->app->memorize('isp_user_import_state',$state_list);
		}
		$state_list = $this->app->recall('isp_user_import_state');
		// echo "<pre>";
		// print_r($plan_list);
		// print_r($country_list);
		// print_r($state_list);
		// print_r($data);
		// echo "</pre>";
		// die();
		
		try{

			$this->api->db->beginTransaction();
			$imported_user_count = 1;
			foreach ($data as $key => $record) {
				
				if(!trim($record['RADIUS_USERNAME'])) continue;
				
				$user = $this->add('xavoc\ispmanager\Model_User');
				// adding hook
				// $user->addHook('afterSave',[$user,'updateUserConditon']);
				// $user->addHook('afterSave',[$user,'createInvoice']);
				// $user->addHook('afterSave',[$user,'updateNASCredential']);
				// $user->addHook('afterSave',[$user,'updateWebsiteUser']);
				$user->addCondition('radius_username',trim($record['RADIUS_USERNAME']));
				$user->tryLoadAny();

				// if($user->loaded()){
				// 	throw new \Exception("user ".$record['RADIUS_USERNAME']." already added ");
				// }

				$plan_name = strtolower(trim($record['PLAN']));
				$plan_id = isset($plan_list[$plan_name])?$plan_list[$plan_name]:0;
				if($plan_id == 0){
					throw new \Exception("User Plan not found".$record['RADIUS_USERNAME'], 1);
				}

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
				if($record['GSTIN']){
					$user['gstin'] = $record['GSTIN'];
				}
				if($record['ORGANIZATION']){
					$user['organization'] = $record['ORGANIZATION'];
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


				if(trim($record['INVOICE_DATE'])){
					// $user->updateUserConditon($expire_all_plan=false,$expire_all_topup=false,$as_grace=true,$record['INVOICE_DATE']);
					$user->setPlan($user['plan_id'],$record['INVOICE_DATE'], $remove_old=false,$is_topup=false,$remove_old_topups=false,$expire_all_plan=false,$expire_all_topup=false,null,$as_grace=true,$force_plan_end_date=$record['PLAN_END_DATE']);
					$user->createInvoice(null,$detail_data=null,$false_condition=false,$master_created_at=$record['INVOICE_DATE'],$force_create=false);
				}else{
					$user->updateUserConditon($expire_all_plan=false,$expire_all_topup=false,$as_grace=true,$on_date=$record['CREATED_AT'],$force_plan_end_date=$record['PLAN_END_DATE']);
					$user->createInvoice(null);
				}
				
				$user->updateNASCredential();
				$user->updateWebsiteUser();

				// data_Remark: eg.dl/up/remark, 1039/209/MainPlan,3089/Topupplan
				if($record['DATA_CONSUMED']){
					$condition_consumed_list = explode(",", $record['DATA_CONSUMED']);

					foreach ($condition_consumed_list as $key => $c_c) {
						$consumed_condition = explode("/", $c_c);
						if(count($consumed_condition) != 3 ) continue;
						
						$dl_data_remaining =  $this->app->human2byte($consumed_condition[0]);
						$up_data_remaining =  $this->app->human2byte($consumed_condition[1]);
						$remark = trim($consumed_condition[2]);

						$plan_condition = $this->add('xavoc\ispmanager\Model_Condition');
						$plan_condition->addCondition('plan_id',$plan_id);
						$plan_condition->addCondition('remark',$remark);
						$plan_condition->tryLoadAny();

						if(!$plan_condition->loaded()) throw new \Exception("Plan Condition not found of plan ".$plan_name." & remark = ".$remark);

						$dl_data_consumed = $this->app->human2byte($plan_condition['data_limit']) - ($dl_data_remaining + $up_data_remaining);

						// echo "data limit = ".$this->app->human2byte($plan_condition['data_limit'])."<br/>";
						// echo "dl_data_remaining = ".$dl_data_remaining."<br/>";
						// echo "up_data_remaining = ".$up_data_remaining."<br/>";
						// echo "dl_data_consumed = ".$dl_data_consumed."<br/>";

						$up_data_consumed = 0;

						$upt = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
						$upt->add('xavoc\ispmanager\Controller_HumanByte')
							->handleFields([
								'download_data_consumed',
								'upload_data_consumed'
							]);
						
						$upt->addCondition('user_id',$user->id);
						// $upt->addCondition('plan_id',$plan_id);
						$upt->setOrder('id','desc');
						$upt->tryLoadAny();
						
						$upt->addCondition('remark',$remark);
						if(!$upt->loaded()){
							echo "<div style='color:red;'> condition not loaded user ".$user['radius_username']."</div><br/>";
							continue;
						}

						$upt['download_data_consumed'] = $dl_data_consumed;
						$upt['upload_data_consumed'] = $up_data_consumed;
						if($record['PLAN_END_DATE']){
							$upt['end_date'] = $record['PLAN_END_DATE'];
							$upt->save();
						}
					}
				}

				// // Static IP 
				if(trim($record['STATIC_IP'])){
					$model = $this->add('xavoc\ispmanager\Model_RadReply');
					$model->addCondition('username',$user['radius_username']);
					$model->addCondition('attribute','Framed-IP-Address');
					$model->addCondition('op',':=');
					$model->tryLoadAny();
					$model['value'] = $record['STATIC_IP'];
					$model->save();
				}

				if(trim($record['MAC_ADDRESS'])){
					$radcheck = $this->add('xavoc\ispmanager\Model_RadCheck');
					$radcheck->addCondition('value',$record['MAC_ADDRESS']);
					$radcheck->addCondition('username', $user['radius_username']);
					$radcheck->addCondition('attribute',"Calling-Station-Id");
					$radcheck->addCondition('op',":=");
					$radcheck->tryLoadAny();
					$radcheck->save();
				}

				echo $imported_user_count." user : ".$user['radius_username']." : $user->id <br/>";
				$imported_user_count++;
			}

			$this->api->db->commit();

		}catch(\Exception $e){
			$this->api->db->rollback();
			
			$this->app->print_r($record);
			throw $e;
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
			throw new \Exception("(".$user->id."= ".$username." = ".$this->id.") user name already use with other isp user ");
		
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
	// invoicePaid functionality shifted to invoiceApproved
	function invoiceApproved($app,$invoice_model){

		if(@$this->app->isp_invoice_approved_function_not_run){
			return;
		}

		$customer = $this->add('xavoc\ispmanager\Model_User');
		$customer->addCondition('id',$invoice_model['contact_id']);
		$customer->tryLoadAny();

		if(!$customer->loaded()) throw new \Exception("customer not found");

		// // $user->addCondition('customer_id',$customer->id)->tryLoadAny();
		// // throw new \Exception($user->id);
		
		$user = $this->add('xavoc\ispmanager\Model_User');
		$user->loadBy('radius_username',$customer['radius_username']);
		
		$items = $invoice_model->Items()->getRows();
		$items_ids = array_column($items, 'item_id');
		$plan = $this->add('xavoc\ispmanager\Model_Plan')
					->addCondition('is_topup',false)
					->addCondition('id',$items_ids)->tryLoadAny();

		if($plan->loaded()){
			$oi = $invoice_model->Items()->tryLoadBy('item_id',$plan->id);
			// code updated on date : 5-May-2018
			// set plan from last plan and condition end date.
			// if not then invoice created at
			$condition_model = $user->getLastCondition();
			if(!$condition_model->loaded()) throw new \Exception("Plan Not Implemented On User ".$this['radius_username']." do it manually");
			$on_date = $condition_model['end_date'];
					// setPlan($plan, $on_date=null, $remove_old=false,$is_topup=false,$remove_old_topups=false,$expire_all_plan=false,$expire_all_topup=false,$work_on_pro_data=true,$as_grace = true,$force_plan_end_date=null,$force_set_plan=false,$set_reset_date=true)
			$user->setPlan($plan->id,$on_date,$remove_old=false,$is_topup=false,$remove_old_topups=false,$expire_all_plan=true,$expire_all_topup=false,!$oi['recurring_qsp_detail_id'],$as_grace= false,null,false,$set_reset_date=false);
		}		
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

	function currentRunningPlan(){
		$upt = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
		$upt->addCondition('user_id',$this->id);
		$upt->addCondition('is_expired',false);
		$upt->addCondition('is_topup',false);
		$upt->setOrder('id','desc');
		$upt->setLimit(1);

		return $upt->ref('plan_id');
	}

	function getLastCondition($only_plan=true){

		$upt = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
		$upt->addCondition('user_id',$this->id);
		if($only_plan)
			$upt->addCondition('is_topup',false);
		$upt->setOrder('id','desc');
		return $upt->tryLoadAny();

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

		$this->app->employee
				->addActivity("Lead '".$this['code']."' assign to employee '".$employee['name']." for installation"."'",null, $this['installation_assign_to_id'] /*Related Contact ID*/,null,null,null)
				->notifyWhoCan('installed','Installation')
				->notifyTo([$this['installation_assign_to_id'],$this['created_by_id']],"Lead '" . $this['code'] ."' Assign to Employee '".$employee['name']." for installation '")
				;

		$this->add('xepan\communication\Model_Communication_Comment')
			->createNew($this->app->employee,$this,"Lead Assign for Installation to ".$employee['name']." by ".$this->app->employee['name'],"Lead Assign for Installation",$on_date=$this->app->now);
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
		
		$payment_model = $this->add('xavoc\ispmanager\Model_PaymentTransaction');
		$payment_model->getElement('employee')->caption('Received By');
		$payment_model->addCondition('contact_id',$this->id);
		$pay_grid = $page->add('xepan\base\Grid');
		$pay_grid->setModel($payment_model,['contact','payment_mode','amount','narration','employee','is_submitted_to_company']);

	}

	function payment_receive($detail_array){
		if(!count($detail_array)) return;

		$emp_id = $this->app->employee->id;
		$channel = $this->add('xepan\base\Model_Contact');
		if($channel->loadLoggedIn('Channel')){
			$emp_id = $channel->id;
		}
		
		$payment = $this->add('xavoc\ispmanager\Model_PaymentTransaction');
		foreach ($detail_array as $field => $value) {
			$payment[$field] = $value;
		}
		
		$payment['contact_id'] = $this->id;
		$payment['employee_id'] = $emp_id;
		$payment->save();

		if($channel->loadLoggedIn()){
			$asso = $this->add('xavoc\ispmanager\Model_Channel_Association');
			$asso['channel_id'] = $channel->id;
			$asso['payment_transaction_id'] = $payment->id;
			$asso->save();
		}

		return $payment;
	}

	function page_installed($page){
		$mandatory_field = ['connection_type'=>'required','customer_type'=>'required'];
		$form = $page->add('xavoc\ispmanager\Form_CAF',['model'=>$this,'mandatory_field'=>$mandatory_field,'show_demoplan'=>false,'change_plan'=>false]);
		if(!$this['radius_username'])
			$form->getElement('radius_username')->set($this['code']);
		// $form->addHook('CAF_AfterSave',function($form)use($page){
		// 	$this->active();
		// 	return $this->app->page_action_result = $this->app->js(true,$page->js()->univ()->closeDialog())->univ()->successMessage('User Activated');
		// });
		try{
			// $this->app->db->beginTransaction();
			if($t=$form->process()){
				$this->installed();
				// $this->app->db->commit();

				if(isset($form->session_item))
					$form->session_item->deleteAll();

				return $this->app->page_action_result = $t;
			}
		}catch(\Exception $e){
			// $this->app->db->rollback();
			throw $e;
		}
	}
	
	function installed(){
		$this['status'] = "Installed";
		$this['installed_at'] = $this->app->now;
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

	function page_active_and_change_plan($page){

		$mandatory_field = [
						'first_name'=>'required',
						'last_name'=>'required',
						'customer_type'=>'required',
						'shipping_country_id'=>'required',
						'shipping_state_id'=>'required',
						'shipping_city'=>'required',
						'shipping_address'=>'required',
						'shipping_pincode'=>'required',

						'radius_username'=>'required',
						'radius_password'=>'required',
						'grace_period_in_days'=>'required',
						'plan_id'=>'required',

					];
		$form = $page->add('xavoc\ispmanager\Form_CAF',['model'=>$this,'mandatory_field'=>$mandatory_field,'manage_consumption'=>false,'show_consumption_detail'=>true,'validate_values'=>false,'allow_invoice'=>true,'show_demoplan'=>true]);

		if(!$this['radius_username'])
			$form->getElement('radius_username')->set($this['code']);

		$form->addHook('CAF_AfterSave',function($form)use($page){

			$invoice_data = $this->active_and_change_plan();
			
			if($form->allow_invoice && $form->invoice_items->count() && $form['create_invoice']){
				$master_model = $invoice_data['master_model'];
				$temp = [];
				foreach ($form->invoice_items as $i_item) {

					$item = $this->add('xepan\commerce\Model_Item')->load($i_item['item']);
					$taxation = $item->applicableTaxation($master_model['shipping_country_id'],$master_model['shipping_state_id']);

					$taxation_id = 0;
					$tax_percentage = 0;
					if($taxation){
						$taxation_id = $taxation['taxation_id'];
						$tax_percentage = $taxation['percentage'];
					}

					$temp[] = [
						'item_id'=>$i_item['item'],
						'price'=>$i_item['amount'],
						'quantity'=>1,
						'taxation_id'=>$taxation_id,
						'tax_percentage'=>$tax_percentage,
						'shipping_charge'=>0,
						'shipping_duration'=>"",
						'express_shipping_charge'=>0,
						'express_shipping_duration'=>"",
						'qty_unit_id'=>$item['qty_unit_id'],
						'discount'=>0,
						'narration'=>$i_item['narration']

					];
				}
				$master_model->addQSPDetail($temp,$master_model);
			}
			
			$form->invoice_items->deleteAll();
			return $this->app->page_action_result = $this->app->js(true,$page->js()->univ()->closeDialog())->univ()->successMessage('User Activated');
		});
		$form->process();
	}

	function active_and_change_plan(){
		// set demo plan id
		if($this['demo_plan_id']){
			$plan_id = $this['demo_plan_id'];
			$this['demo_plan_id']  = null;
			$status = 'InDemo';
		}else{
			$plan_id = $this['plan_id'];
			$status = 'Active';
		}
		
		// current plan id not same as new plan id
		//  and if reset_same_plan_again is define then reset the plan agian on reset date if define else 
		if((($p = $this->currentRunningPlan())&& $p->id != $plan_id) OR @$this->app->reset_same_plan_again){
			$on_date = null;
			if(@$this->app->reset_same_plan_again_on_date){
				$on_date = $this->app->reset_same_plan_again_on_date;
			}
				 //setPlan($plan, $on_date=null, $remove_old=false,$is_topup=false,$remove_old_topups=false,$expire_all_plan=false,$expire_all_topup=false,$work_on_pro_data=true,$as_grace = true,$force_plan_end_date=null,$force_set_plan=false)
			$this->setPlan($plan_id,$on_date,null,null,null,$expire_all_plan=true,null,null,$as_grace=false,null,$force_set_plan=true);
		}
		
		$this['status'] = $status;
		$this['is_active'] = true;
		$this->save();

		// $this->updateUserConditon();
		$return_data = $this->createInvoice($this,null,null,$this->app->now);

		if(isset($return_data['master_detail'])){
			$invoice_model = $this->add('xepan\commerce\Model_SalesInvoice')
					->load($return_data['master_detail']['id']);
			// as per logic.jade it is due in status here by default
			$invoice_model['status'] = 'Due';
			$invoice_model->save();
		}

		$this->updateNASCredential();
		$this->updateWebsiteUser();

		return $return_data;
	}

	function page_Reset_Current_Plan_Condition($page){
		if(!$this['plan_id']) throw new Exception("plan not added to user");
		
		$last_end_date = $this->ref('PlanConditions')->setLimit(1)->setOrder('end_date','desc')->tryLoadAny()->get('end_date');

		$form = $page->add('Form');
		$form->addField('DatePicker','from_date')->set($last_end_date)->setFieldHint('Previous plan end date was '. $last_end_date)->validate('required');
		$form->addSubmit('Reset');

		if($form->isSubmitted()){
			if(strtotime($form['from_date']) < strtotime($last_end_date))
				$form->displayError('from_date','Cannot Reset before previous end date');

			$this->setPlan($this['plan_id'],$form['from_date']);
			$this->updateNASCredential();
			$this->updateWebsiteUser();
			
			return $this->app->page_action_result = $this->app->js(true,$page->js()->univ()->closeDialog())->univ()->successMessage('User Plan Condition Resetted');
			
		}
	}

	function page_radius_attributes($page){

		$tab = $page->add('Tabs');
		$sip_tab = $tab->addTab('Static IP');
		$mac_tab = $tab->addTab('MAC Address Bind');
		$udc_tab = $tab->addTab('User Data Consumed');
		$ippool_tab = $tab->addTab('IP Pool');

		$model = $this->add('xavoc\ispmanager\Model_RadReply');
		$model->addCondition('username',$this['radius_username']);
		$model->addCondition('attribute','Framed-IP-Address');
		$model->addCondition('op',':=');

		$crud = $sip_tab->add('xepan\base\CRUD',['entity_name'=>'Static IP','allow_edit'=>false]);
		if($model->count()->getOne() >= 1){
			$crud->allow_add = false;
		}
		$crud->setModel($model);

		$mac_address = $this['mac_address'];
		if($_GET['recent_mac_address'])
			$mac_address = $_GET['recent_mac_address'];

		$form = $mac_tab->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->showLables(true)
			->makePanelsCoppalsible(true)
			->layout([
				'mac_address'=>'Update Mac Address~c1~12',
				'FormButtons~&nbsp;'=>'c2~12'
			]);

		$form->addField('line','mac_address')->set($mac_address);

		$get_mac_btn = $form->addSubmit('Get Mac Address')->addClass('btn btn-warning');
		$bind_mac_btn = $form->addSubmit('Bind Mac Address')->addClass('btn btn-primary');
		$release_mac_btn = $form->addSubmit('Release Mac Address')->addClass('btn btn-danger');

		if($form->isSubmitted()){

			if($form->isClicked($get_mac_btn)){
				$data_model = $this->getRecentRadAcct();
				$form->js()->reload(['recent_mac_address'=>$data_model['callingstationid']])->execute();
			}

			if($form->isClicked($bind_mac_btn)){
				if(!$form['mac_address']){
					$form->error('mac_address','mac address must not be empty');
				}

				$radcheck = $this->add('xavoc\ispmanager\Model_RadCheck');
				$radcheck->addCondition('value',$form['mac_address']);

				if($radcheck->count()->getOne() > 1){
					$name = "";
					foreach ($radcheck as $model) {
						$name .= $model['username'].",";
					}
					$form->error('mac_address','mac_address associate with multiple user named '.$name);
				}

				$radcheck->tryLoadAny();
				
				if($radcheck->loaded() AND $radcheck['username'] != $this['radius_username'])
					$form->error('mac_address','mac_address associate with other user named '.$radcheck['username']);

				$radcheck['username'] = $this['radius_username'];
				$radcheck['attribute'] = "Calling-Station-Id";
				$radcheck['op'] = ":=";
				$radcheck->save();

				$this['mac_address'] = $form['mac_address'];
				$this->save();

				return $this->app->page_action_result = $form->js(null,$form->js()->reload())->univ()->successMessage('Mac Address Updated');
			}

			if($form->isClicked($release_mac_btn)){
				if(!$form['mac_address']){
					$form->error('mac_address','mac address must not be empty');
				}

				$this->releaseMacAddress($form['mac_address']);
				return $this->app->page_action_result = $form->js(null,$form->js()->reload())->univ()->successMessage('Mac Address Removed');
			}

		}


		$udc_tab->add('xavoc\ispmanager\View_UserDataConsumption',['username'=>$this['radius_username']]);
			
		// ip pool
		$pool_model = $this->add('xavoc\ispmanager\Model_RadReply');
		$pool_model->addCondition('username',$this['radius_username']);
		$pool_model->addCondition('attribute','Framed-Pool');
		$pool_model->addCondition('op',':=');
		$crud = $ippool_tab->add('xepan\base\CRUD',['entity_name'=>'IP Pool']);
		$crud->setModel($pool_model);
	}

	function bindMacAddress($mac_address){
		$radcheck = $this->add('xavoc\ispmanager\Model_RadCheck');
		$radcheck->addCondition('value',$mac_address);

		if($radcheck->count()->getOne() > 1){
			$name = "";
			foreach ($radcheck as $model) {
				$name .= $model['username'].",";
			}
			throw new \Exception('mac_address associate with multiple user named '.$name);
		}
		
		$radcheck->tryLoadAny();
		if($radcheck->loaded() AND $radcheck['username'] != $this['radius_username'])
			throw new \Exception('mac_address associate with other user named '.$radcheck['username']);

		$radcheck['username'] = $this['radius_username'];
		$radcheck['attribute'] = "Calling-Station-Id";
		$radcheck['op'] = ":=";
		$radcheck->save();

		$this['mac_address'] = $mac_address;
		$this->save();

		return $radcheck;
	}

	function releaseMacAddress($mac_address){
		$radcheck = $this->add('xavoc\ispmanager\Model_RadCheck');
		$radcheck->addCondition('value',$mac_address);
		$radcheck->addCondition('username',$this['radius_username']);
		$radcheck->addCondition('attribute','Calling-Station-Id');
		$radcheck->addCondition('op',':=');
		$radcheck->tryLoadAny();

		if(!$radcheck->loaded())
			throw new \Exception("no one bind mac address found");
		
		$radcheck->delete();

		$this['mac_address'] = "";
		$this->save();
	}


	function deactivate(){
		$this['status'] = 'InActive';
		$this['is_active'] = false;
		$this->app->employee
            ->addActivity("Customer : '". $this['name'] ."' has been deactivated", null /*Related Document ID*/, $this->id /*Related Contact ID*/,null,null,"xepan_commerce_customerdetail&contact_id=".$this->id."")
            ->notifyWhoCan('activate','InActive',$this);
		return $this->save();
	}

	function getRecentRadAcct(){
		if(!$this->loaded()) return;

		$radacct_m = $this->add('xavoc\ispmanager\Model_RadAcct');
		$radacct_m->addCondition('username',$this['radius_username']);
		$radacct_m->setOrder('radacctid','desc');
		$radacct_m->setLimit(1);
		$radacct_m->tryLoadAny();
		return $radacct_m;
	}

	function explanation($page){
		$v = $page->add('View');
		$ht = "<div class='alert alert-info'>Regular Plan: Data Limit 200GB @ 4 MB/No Fup, for 1 Month<br/>";
		$ht .= "Extra Topup: Data Limit 50GB  @ 20MB/8MB Fup, for 8 Days</div>";
		$ht .= "<div class='alert alert-danger'>if this option is <b>off</b>: 50GB  @ 20MB and then 8mbps for unlimited data for rest of days (if left from 8 days) and then back on reglar plan</div>";
		$ht .= "<div class='alert alert-success'>if this option is <b>ON</b>: 50GB  @ 20MB and then 8mbps, but data from 200GB is consumed [for rest of days (if left from 8 days) then back on regular plan]<br/> if that 200GB is finished, net disconnected or will work on 200Gb FUP(if exist)</div>";
		
		$v->setHtml($ht);
	}

	function print_caf(){
		$js = $this->app->js()->univ()->newWindow($this->app->url('xavoc_ispmanager_cafprint',['contact_id'=>$this->id]),'PrintCAF'.$this->id);
		$this->app->js(null,$js)->univ()->execute();
	}

	function page_challan($page){

		$grid = $page->add('xepan\hr\Grid');
		$issue_model = $this->add('xepan\commerce\Model_Store_TransactionAbstract')
					->addCondition('type','Issue')
					->addCondition('to_warehouse_id',$this->id)
					;
		$issue_model->getElement('from_warehouse')->caption('From');
		$issue_model->getElement('to_contact_name')->caption('To');

		$grid->setModel($issue_model,['to_contact_name','from_warehouse','item_quantity','created_at']);
		$grid->addPaginator($ipp=25);
		$grid->addSno();
		$print_btn = $grid->addColumn('Button','Print_Document');

		if($transaction_id = $_GET['Print_Document']){
			$this->app->js(true)->univ()->newWindow($this->app->url('xepan_commerce_printstoretransaction',['transaction_id'=>$transaction_id]),'PrintIssueChallan')->execute();
		}

		// details
		$grid->add('VirtualPage')
		->addColumn('Details')
		->set(function($page){
			$id = $_GET[$page->short_name.'_id'];
			$detail_model = $page->add('xepan\commerce\Model_Store_TransactionRow');
			$detail_model->addCondition('store_transaction_id',$id);

			$grid = $page->add('xepan\hr\Grid');
			$grid->setModel($detail_model,['item_name','quantity','extra_info','serial_nos','transaction_narration','item_qty_unit']);
			$grid->addPaginator(10);
		});
	}

	function personal_info(){
		$this->app->js()->univ()->frameURL('User Personal Info',
			$this->app->url('xepan_commerce_customerdetail',
					['action'=>'edit','contact_id'=>$this->id]
				))->execute();
	}

	function page_lost($page){

		$config = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'lead_lost_region'=>'text'
						],
					'config_key'=>'ISPMANAGER_MISC',
					'application'=>'ispmanager'
			]);
		// $config->add('xepan\hr\Controller_ACL');
		$config->tryLoadAny();
		$temp = explode(",", $config['lead_lost_region']);
		$regions = [];
		foreach ($temp as $key => $value) {
			$regions[$value] = $value;
		}

		$form = $page->add('Form');
		$region_field = $form->addField('xepan\base\DropDown','region')->validate('required');
		$region_field->setValueList($regions);
		$region_field->setEmptyText('Please Select');

		$form->addField('text','narration');
		$form->addSubmit('Save');
		if($form->isSubmitted()){
			$region = $form['region'];
			if($form['narration'])
				$region .= "::".$form['narration'];
			
			$this->lost($region);
			// delete isp user entry
			$query = "delete from isp_user where customer_id =".$this->id.";";
			$query .= "delete from customer where contact_id =".$this->id.";";
			$query .= "update contact set type='Contact' where id=".$this->id.";";
			$this->app->db->dsql()->expr($query)->execute();

			return $this->app->page_action_result = $this->app->js(true,$page->js()->univ()->closeDialog())->univ()->errorMessage('Lead Lost');	
		}
	}

	function beforeQspDocumentGenerate($app,&$qsp_model){
		$isp_user = $this->newInstance()->tryLoad($qsp_model['contact_id']);
		foreach ($isp_user->getActualFields() as $field) {
			$qsp_model['isp_'.$field]= $isp_user[$field];
		}

	}

	function lost($remark){
		if(!$this->loaded()) throw new \Exception("lead model must loaded");

		$this['remark'] = $this['remark']." ".$remark;
		$this['status'] = "Lost";
		$this->save();

		$this->add('xepan\communication\Model_Communication_Comment')
			->createNew($this->app->employee,$this,"Lead Lost by ".$this->app->employee['name'],$remark,$on_date=$this->app->now);
	}

	function close_session(){
		$query = "UPDATE radacct SET acctstoptime = acctupdatetime, acctterminatecause='Session-closed manually' WHERE username = '".$this['radius_username']."' AND acctstoptime is null;";
		$this->app->db->dsql()->expr($query)->execute();
		return $this->app->js()->univ()->successMessage('Session Closed Successfully');
	}
}
