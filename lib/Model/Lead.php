<?php

namespace xavoc\ispmanager;

class Model_Lead extends \xepan\marketing\Model_Lead{ 
	
	public $status = ['Active','InActive'];
	public $actions = [
					'Active'=>['view','edit','delete','communication','assign','createUser','send','manage_score','deactivate'],
					'InActive'=>['view','edit','delete','activate','communication','manage_score']
					];

	function init(){
		parent::init();
		
	}

	function page_assign($page){
		$emp = $page->add('xepan\hr\Model_Employee');
		$emp->addCondition('id','<>',$this->id);

		$form = $page->add('Form');
		$emp_field = $form->addField('xepan\base\DropDown','employee')->validate('required');
		$emp_field->setModel($emp);
		$form->addSubmit('Assign')->addClass('btn btn-primary');
		if($form->isSubmitted()){
			$this['assign_to_id'] = $form['employee'];
			$this->save();
			return $this->app->page_action_result = $this->app->js(true,$page->js()->univ()->closeDialog())->univ()->successMessage('Assigned');
		}

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
			
			try{
				$this->app->db->beginTransaction();
				// insert customer
				$cust_q = "INSERT into customer (contact_id, billing_country_id, billing_state_id, billing_city, billing_address, billing_pincode, shipping_country_id, shipping_state_id, shipping_city, shipping_address, shipping_pincode, same_as_billing_address ) VALUES (".$this->id.",".$form['billing_country'].",".$form['billing_state'].",'".$form['billing_city']."','".$form['billing_address']."','".$form['billing_pincode']."',".$shipping_country.",".$shipping_state.",'".$shipping_city."','".$shipping_address."','".$shipping_pincode."','".$form['shipping_address_same_as_billing_address']."')";
				$this->app->db->dsql()->expr($cust_q)->execute();

				// insert user
				$isp_user_q = "INSERT into isp_user (customer_id,radius_username, radius_password, first_name, last_name, contact_number, email_id, created_at) VALUES (".$this->id.",'".$form['radius_username']."','".$form['radius_password']."','".$form['first_name']."','".$form['last_name']."','".$form['mobile_no']."','".$form['email_id']."','".$this->app->now."')";
				$this->app->db->dsql()->expr($isp_user_q)->execute();

				$user = $this->add('xavoc\ispmanager\Model_User');
				$user->addCondition('customer_id',$this->id);
				$user->tryLoadAny();

				$user['plan_id'] = $form['plan'];
				$user['create_invoice'] = $form['create_invoice'];
				$user['is_invoice_date_first_to_first'] = $form['is_invoice_date_first_to_first'];
				$user['grace_period_in_days'] = $form['grace_period_in_days'];
				$user->save();

				$this->app->db->commit();
			}catch(\Exception $e){
				$this->app->db->rollback();
				throw $e;
			}

			return $this->app->page_action_result = $this->app->js(true,$page->js()->univ()->closeDialog())->univ()->successMessage('user created successfully');
		}
	}

}