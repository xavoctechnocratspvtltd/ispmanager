<?php

namespace xavoc\ispmanager;

class Model_Lead extends \xepan\marketing\Model_Lead{ 
	
	public $status = ['Active','InActive'];
	public $actions = [
					'Active'=>['view','edit','delete','assign','deactivate','communication'],
					'Open'=>['view','edit','delete','create_user','communication'], //need analysi
					'Won'=>['view','edit','delete','communication'],
					'Lost'=>['view','edit','delete','open','communication'],
					'InActive'=>['view','edit','delete','activate','communication']
				];
	public $acl_type="ispmanager_Lead";				

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

	function assign($assign_to_id){

		$this['assign_to_id'] = $assign_to_id;
		$this['status'] = "Open";
		$this->save();

		$employee = $this->add('xepan\hr\Model_Employee')->load($assign_to_id);
		// send email and sms
		$this->add('xavoc\ispmanager\Controller_Greet')->do($employee,'lead_assign',$this);
		return $this;
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

	function page_createUser($page){
		$this->app->stickyGET('b_country_id');
		$this->app->stickyGET('s_country_id');

		$isp_user = $page->add('xavoc\ispmanager\Model_User');
		$isp_user->addCondition('customer_id',$this->id);
		if($isp_user->count()->getOne()){
			$page->add('View')
				->addClass('alert alert-danger')
				->set('isp user already exists')
			;
			return;
		}

		$plan = $page->add('xavoc\ispmanager\Model_Plan');
		$plan->addCondition('status','Published');

		$form = $page->add('Form');
		$form->setLayout('form/createuser');

		$plan_field = $form->addField('xepan\base\DropDown','plan');
		$plan_field->setModel($plan);
		
		$form->addField('radius_username')->validate('required');
		$form->addField('radius_password')->validate('required');
		$form->addField('mobile_no')->validate('required');
		$form->addField('email_id')->validate('required');

		$form->addField('first_name')->set($this['first_name']);
		$form->addField('last_name')->set($this['last_name']);

		// billing address
		$b_c_model = $this->add('xepan\base\Model_Country');
		$b_s_model = $this->add('xepan\base\Model_State');

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
		$s_s_model = $this->add('xepan\base\Model_State');

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

		$form->addField('checkbox','create_invoice');
		$form->addField('checkbox','is_invoice_date_first_to_first');
		$form->addField('Number','grace_period_in_days');

		$form->addSubmit('create user')->addClass('btn btn-primary');
		
		if($form->isSubmitted()){

			$isp_user = $page->add('xavoc\ispmanager\Model_User');
			$isp_user->addCondition('customer_id',$this->id);
			if($isp_user->count()->getOne()){
				$form->js()->univ()->errorMessage('user already exists')->execute();
			}

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
			// 	$this->app->db->beginTransaction();
				// insert customer
				$this['type'] = "Customer";
				$this->save();

				$cust_q = "INSERT into customer (contact_id, billing_country_id, billing_state_id, billing_city, billing_address, billing_pincode, shipping_country_id, shipping_state_id, shipping_city, shipping_address, shipping_pincode, same_as_billing_address ) VALUES (".$this->id.",".$form['billing_country'].",".$form['billing_state'].",'".$form['billing_city']."','".$form['billing_address']."','".$form['billing_pincode']."',".$shipping_country.",".$shipping_state.",'".$shipping_city."','".$shipping_address."','".$shipping_pincode."','".$form['shipping_address_same_as_billing_address']."')";
				$this->app->db->dsql()->expr($cust_q)->execute();

				// insert user
				$isp_user_q = "INSERT into isp_user (customer_id,radius_username, radius_password, first_name, last_name, contact_number, email_id, created_at) VALUES (".$this->id.",'".$form['radius_username']."','".$form['radius_password']."','".$form['first_name']."','".$form['last_name']."','".$form['mobile_no']."','".$form['email_id']."','".$this->app->now."')";
				$this->app->db->dsql()->expr($isp_user_q)->execute();

				$user = $this->add('xavoc\ispmanager\Model_User');
				$user->addCondition('id',$this->id);
				$user->tryLoadAny();
				
				if($user->loaded()){
					$user['plan_id'] = $form['plan'];
					$user['create_invoice'] = $form['create_invoice'];
					$user['is_invoice_date_first_to_first'] = $form['is_invoice_date_first_to_first'];
					$user['grace_period_in_days'] = $form['grace_period_in_days'];
					$user->save();
				}

				// $this->app->db->commit();
			// }catch(\Exception $e){
			// 	$this->app->db->rollback();
			// 	throw $e;
			// }

			return $this->app->page_action_result = $this->app->js(true,$page->js()->univ()->closeDialog())->univ()->successMessage('user created successfully');
		}
	}

}