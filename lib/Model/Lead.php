<?php

namespace xavoc\ispmanager;

class Model_Lead extends \xepan\marketing\Model_Lead{ 
	
	public $status = ['Active','InActive'];
	public $actions = [
					'Active'=>['view','assign','deactivate','communication','edit','delete'],
					'Open'=>['view','assign','close','lost','communication','edit','delete'],
					'Won'=>['view','edit','delete','communication'],
					'Lost'=>['view','open','communication','edit','delete'],
					'InActive'=>['view','edit','delete','activate','communication']
				];

	// public $acl_type = "ispmanager_Lead";

	// 'createUser','send','manage_score','due_invoice','change_plan',
	
	function init(){
		parent::init();
		
		// $this->addHook('beforeSave',$this);
	}


	function beforeSave(){
		// if($this['status'] == "Active" AND $this['assign_to_id'] > 0){
		// 	$this['status'] = "Open";
		// }
	}

	function assign($assign_to_id,$remark=null){

		$this['assign_to_id'] = $assign_to_id;
		$this['remark'] .= " ".$remark;
		$this['status'] = "Open";
		$this['assign_at'] = $this->app->now;
		$this->save();

		$employee = $this->add('xavoc\ispmanager\Model_Employee')
					->load($assign_to_id);
		// send email and sms
		$this->add('xavoc\ispmanager\Controller_Greet')->do($employee,'lead_assign',$this);
		
		// $this->app->employee
		//         ->addActivity("Lead '".$this['code']."' assign to '".$employee['name']."'",null, $this['assign_to_id'] /*Related Contact ID*/,null,null,null)
		//         ->notifyWhoCan('close,lost','Open')
		//         ->notifyTo([$this['created_by_id']],"Lead : '" . $this['code'] ."' Assign to '".$employee['name']." by ".$this->app->employee['name']."'")
		//         ;
		// return $this;
	}

	function page_open($page){

		$dept_id = $this->app->stickyGET('dept_id');
		$emp = $page->add('xepan\hr\Model_Employee');
		$emp->addCondition('id','<>',$this->app->employee->id);
		
		$form = $page->add('Form');
		$dept = $page->add('xepan\hr\Model_Department');
		$dept->addCondition('status','Active');

		$dept_field = $form->addField('xepan\base\DropDown','department');
		$dept_field->setModel($dept);
		$dept_field->setEmptyText('Please Select Department');

		$emp_field = $form->addField('xepan\base\DropDown','employee')->validate('required');
		$emp_field->setModel($emp);
		$emp_field->setEmptyText('Please Select');

		$form->addField('text','remark');

		if($this['assign_to_id'])
			$emp_field->set($this['assign_to_id']);

		if($dept_id){
			$emp_field->getModel()->addCondition('department_id',$dept_id);
		}

		$dept_field->js('change',$form->js()->atk4_form('reloadField','employee',[$this->app->url(),'dept_id'=>$dept_field->js()->val()]));
		// $dept_field->js('change',$emp_field->js()->reload(null,null,[$this->app->url(null,['cut_object'=>$emp_field->name]),'dept_id'=>$dept_field->js()->val()]));

		$form->addSubmit('Re-open & Assign')->addClass('btn btn-primary');
		if($form->isSubmitted()){
			$this->open($form['employee'],$form['remark']);
			return $this->app->page_action_result = $this->app->js(true,$page->js()->univ()->closeDialog())->univ()->successMessage('Assigned');
		}
	}

	function open($assign_to_id,$remark=null){
		$this['assign_to_id'] = $assign_to_id;
		$this['remark'] .= " ".$remark;
		$this['status'] = "Open";
		$this['assign_at'] = $this->app->now;
		$this->save();

		$employee = $this->add('xepan\hr\Model_Employee')->load($assign_to_id);
		// send email and sms
		$this->add('xavoc\ispmanager\Controller_Greet')->do($employee,'lead_assign',$this);
		return $this;
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
		$config->add('xepan\hr\Controller_ACL');
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
			return $this->app->page_action_result = $this->app->js(true,$page->js()->univ()->closeDialog())->univ()->errorMessage('Lead Lost');	
		}
	}

	function lost($remark){
		if(!$this->loaded()) throw new \Exception("lead model must loaded");

		$this['remark'] = $this['remark']." ".$remark;
		$this['status'] = "Lost";
		$this->save();
	}


	function page_assign_for_installation($page){

	}


	function page_change_plan($page){
		$isp_user = $page->add('xavoc\ispmanager\Model_User');
		$isp_user->addCondition('customer_id',$this->id);		
		$isp_user->tryLoadAny();
		if(!$isp_user->loaded()){
			$page->add('View')->set('User not Exist ( Please Create User First)')->addClass('py-1 bg-success');
			return ;
		}

		$plan = $page->add('xavoc\ispmanager\Model_Plan');
		$plan->addCondition('status','Published');
		// $plan->addCondition('id',$isp_user['plan_id']);

		$form = $page->add('Form');
		$form->setLayout('form/changeplan');
		$plan_field = $form->addField('xepan\base\DropDown','plan');
		$plan_field->setModel($plan);
		$plan_field->set($isp_user['plan_id']);
		$form->addField('Checkbox','create_invoice','');
		$form->addSubmit('Change Plan')->addClass('btn btn-primary');

		if($form->isSubmitted()){
			$isp_user['plan_id'] = $form['plan'];
			$isp_user['create_invoice'] = $form['create_invoice'];
			$isp_user->save();

			return $this->app->page_action_result = $this->app->js(true,$page->js()->univ()->closeDialog())->univ()->successMessage('Plan Chnaged Successfully');
		}
		
	}

	function page_due_invoice($page){
		$invoice = $page->add('xavoc\ispmanager\Model_Invoice');
		$invoice->addCondition('contact_id',$this->id);
		$invoice->addCondition('status',"<>",'Paid');

		$g = $page->add('xepan\base\Grid',null,null,['grid/due-invoice']);
		$g->setModel($invoice);
		$pay_btn = $g->addColumn('Button','Pay_Now');

		$g->addMethod('format_Pay_Now',function($g,$f){
				$g->current_row_html['Pay_Now']= '<a href="javascript:void(0)" onclick="'.$g->js()->univ()->newWindow($this->app->url('staff_received-payment',['invoice_id'=>$g->model->id,'customer_id'=>$this->id])).'"><span class="btn btn-success">Pay Now</span></a>';
		});
		$g->addFormatter('Pay_Now','Pay_Now');

	}

	function page_close($page){
		
		// echo "type= ".$this['type']." = id=".$this['id']."<br/>";
		$this->app->stickyGET('b_country_id');
		$this->app->stickyGET('s_country_id');

		$isp_user = $page->add('xavoc\ispmanager\Model_User');
		$isp_user->addCondition('customer_id',$this->id);
		if($isp_user->count()->getOne()){
			$isp_user->tryLoadAny();
			$page->add('View')
				->addClass('alert alert-danger')
				->set('isp user '.$isp_user['radius_username'].' already exists')
			;
			// return;
		}

		$plan = $page->add('xavoc\ispmanager\Model_Plan');
		if($page->add('xavoc\ispmanager\Model_Channel')->loadLoggedIn()){
			$plan = $page->add('xavoc\ispmanager\Model_Channel_Plan');
		}
		$plan->addCondition('status','Published');

		$form = $page->add('Form');
		// $form->add('xepan\base\Controller_FLC')
		// 		->showLables(true)
		// 		->addContentSpot()
		// 		->makePanelsCoppalsible(true)
		// 		->layout([
		// 				'plan~Plan'=>'User Plan Information~c1~12',

		// 				'first_name'=>'User Information~c1~6',
		// 				'last_name'=>'c2~6',
		// 				'mobile_no'=>'c3~6',
		// 				'email_id'=>'c4~6',
		// 				'billing_country'=>'c5~3'
		// 			]);
		$form->setLayout('form/createuser');

		$plan_field = $form->addField('xepan\base\DropDown','plan')->validate('required');
		$plan_field->setModel($plan);
		$plan_field->setEmptyText('Please Select');
		
		$form->addField('mobile_no')->validate('required')->set($this->getPhones()[0]);
		$form->addField('email_id')->validate('required')->set($this->getEmails()[0]);

		$form->addField('first_name')->set($this['first_name']);
		$form->addField('last_name')->set($this['last_name']);

		// billing address
		$b_c_model = $this->add('xepan\base\Model_Country')->addCondition('status','Active');
		$b_s_model = $this->add('xepan\base\Model_State')->addCondition('status','Active');

		$b_c_field = $form->addField('xepan\base\DropDown','billing_country')->validate('required');
		$b_c_field->setModel($b_c_model);
		$b_c_model->set($this['country_id']);

		$b_s_field = $form->addField('xepan\base\DropDown','billing_state')->validate('required');
		$b_s_field->setModel($b_s_model);
		$b_s_field->set($this['state_id']);

		// billing country change event
		if($_GET['b_country_id']){
			$b_s_field->getModel()->addCondition('country_id',$_GET['b_country_id']);
		}
		$b_c_field->js('change',$form->js()->atk4_form('reloadField','billing_state',[$this->app->url(null,['cut_object'=>$b_s_field->name]),'b_country_id'=>$b_c_field->js()->val()]));

		$form->addField('billing_city')->validate('required')->set($this['city']);

		$form->addField('text','billing_address')
				->validate('required')
				->set($this['address']);

		$form->addField('billing_pincode')
				->validate('required')
				->set($this['pin_code']);

		$same_field = $form->addField('checkbox','shipping_address_same_as_billing_address')->set(1);

		$same_field->js(true)->univ()->bindConditionalShow([
				''=>['shipping_country','shipping_state','shipping_city','shipping_pincode','shipping_address'],
				'*'=>['']				
			],'div.atk-form-row');

		// shipping address
		$s_c_model = $this->add('xepan\base\Model_Country');
		$s_c_model->addCondition('status','Active');
		
		$s_s_model = $this->add('xepan\base\Model_State');
		$s_s_model->addCondition('status','Active');

		$s_c_f = $form->addField('xepan\base\DropDown','shipping_country');
		$s_c_f->setModel($s_c_model);

		$s_s_f = $form->addField('xepan\base\DropDown','shipping_state');
		$s_s_f->setModel($s_s_model);

		if($_GET['s_country_id']){
			$s_s_f->getModel()->addCondition('country_id',$_GET['s_country_id']);
		}
		$s_c_f->js('change',$form->js()->atk4_form('reloadField','shipping_state',[$this->app->url(null,['cut_object'=>$s_s_f->name]),'s_country_id'=>$s_c_f->js()->val()]));

		$form->addField('text','shipping_address');
		$form->addField('shipping_city');
		$form->addField('shipping_pincode');

		$config = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'attachment_type'=>'text'
						],
					'config_key'=>'ISPMANAGER_MISC',
					'application'=>'ispmanager'
			]);
		$config->add('xepan\hr\Controller_ACL',['permissive_acl'=>true]);
		$config->tryLoadAny();
		
		if($config['attachment_type']){
			$attachment_type = explode(",", $config['attachment_type']);

			foreach ($attachment_type as $key => $value) {
				$field = $form->addField('xepan\base\Upload',$this->app->normalizeName($value),$value);
				$field->setModel('xepan\filestore\Image');
			}
		}

		// $form->addField('checkbox','create_invoice');
		// $form->addField('checkbox','is_invoice_date_first_to_first');
		// $form->addField('Number','grace_period_in_days');

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

		$form->addSubmit('create user')->addClass('btn btn-primary');
		
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
			// $isp_user = $page->add('xavoc\ispmanager\Model_User');
			// $isp_user->addCondition('customer_id',$this->id);
			// if($isp_user->count()->getOne()){
			// 	$form->js()->univ()->errorMessage('user already exists')->execute();
			// }

			$shipping_country = $form['billing_country'];
			$shipping_state = $form['billing_state'];
			$shipping_city = $form['billing_city'];
			$shipping_address = $form['billing_address'];
			$shipping_pincode = $form['billing_pincode'];

			if(!$form['shipping_address_same_as_billing_address']){
				if(!$form['shipping_country']) $form->error('shipping_country','must not be empty');
				if(!$form['shipping_state']) $form->error('shipping_state','must not be empty');
				if(!$form['shipping_city']) $form->error('shipping_city','must not be empty');
				if(!$form['shipping_address']) $form->error('shipping_address','must not be empty');
				if(!$form['shipping_pincode']) $form->error('shipping_pincode','must not be empty');

				$shipping_country = $form['shipping_country'];
				$shipping_state = $form['shipping_state'];
				$shipping_city = $form['shipping_city'];
				$shipping_address = $form['shipping_address'];
				$shipping_pincode = $form['shipping_pincode'];
			}
			
			// try{
				// $this->app->db->beginTransaction();
				// insert customer
				$this['type'] = "Customer";
				$this->save();

				$cust_q = "INSERT into customer (contact_id, billing_country_id, billing_state_id, billing_city, billing_address, billing_pincode, shipping_country_id, shipping_state_id, shipping_city, shipping_address, shipping_pincode, same_as_billing_address ) VALUES (".$this->id.",".$form['billing_country'].",".$form['billing_state'].",'".$form['billing_city']."','".$form['billing_address']."','".$form['billing_pincode']."',".$shipping_country.",".$shipping_state.",'".$shipping_city."','".$shipping_address."','".$shipping_pincode."','".$form['shipping_address_same_as_billing_address']."')";
				$this->app->db->dsql()->expr($cust_q)->execute();

				// insert user
				$isp_user_q = "INSERT into isp_user (customer_id, first_name, last_name, contact_number, email_id, created_at) VALUES (".$this->id.",'".$form['first_name']."','".$form['last_name']."','".$form['mobile_no']."','".$form['email_id']."','".$this->app->now."')";
				$this->app->db->dsql()->expr($isp_user_q)->execute();
				
				$user = $this->add('xavoc\ispmanager\Model_User');
				$user->addCondition('id',$this->id);
				$user->tryLoadAny();
				
				if($user->loaded()){
					$user['plan_id'] = $form['plan'];
					$user['create_invoice'] = 0;
					// $user['create_invoice'] = $form['create_invoice'];
					// $user['is_invoice_date_first_to_first'] = $form['is_invoice_date_first_to_first'];
					// $user['grace_period_in_days'] = $form['grace_period_in_days'];
					$user->save();

					// attachment entry
					if(isset($attachment_type)){
						foreach ($attachment_type as $key => $value) {
							$attachment_name = $this->app->normalizeName($value);
							if($form[$attachment_name]){
								$attachment = $this->add('xavoc\ispmanager\Model_Attachment');
								$attachment->addCondition('contact_id',$user->id);
								$attachment->addCondition('title',$attachment_name);
								$attachment->tryLoadAny();

								$attachment['file_id'] = $form[$attachment_name];
								$attachment->save();
							}
						}
					}
				}

				$this->updatePaymentTransaction($payment_detail);

				$channel = $this->add('xepan\base\Model_Contact');
				if($channel->loadLoggedIn('Channel')){
					$asso = $this->add('xavoc\ispmanager\Model_Channel_Association');
					$asso['channel_id'] = $channel->id;
					$asso['isp_user_id'] = $this->id;
					$asso->save();
				}
				
				$this->close();
			// 	$this->app->db->commit();
			// }catch(\Exception $e){
			// 	$this->app->db->rollback();
			// 	throw $e;
			// }
			return $this->app->page_action_result = $this->app->js(true,$page->js()->univ()->closeDialog())->univ()->successMessage('user created successfully');
		}
	}

	function close(){

		$this['status'] = "Won";
		$this->save();
		
		$this->app->employee
        	->addActivity("Lead '".$this['code']."' Closed by '".$this->app->employee['name']."'",null, $this['id'] /*Related Contact ID*/,null,null,null)
			->notifyWhoCan('view,edit,assign_for_installation','Won');
        	// ->notifyTo([$this['created_by_id']],"Lead : '" . $this['code'] ."' Closed by '".$this->app->employee['name']);

        return $this;
		// $this->add('xavoc\ispmanager\Controller_Greet')->do($this,'lead_won');
	}


	function updatePaymentTransaction($detail_array){
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

}