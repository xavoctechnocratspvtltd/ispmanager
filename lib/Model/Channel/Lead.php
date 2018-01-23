<?php

namespace xavoc\ispmanager;

class Model_Channel_Lead extends \xavoc\ispmanager\Model_Lead{ 
	
	function init(){
		parent::init();
		
		$join = $this->join('isp_channel_association.lead_id');
		$join->addField('channel_id');
		
		$this->getElement('status')
			->defaultValue('Open');
	}

	function page_close($page){

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

		$plan = $page->add('xavoc\ispmanager\Model_Channel_Plan');
		$plan->addCondition('status','Published');
		
		$form = $page->add('Form');
		$form->setLayout('form/createuser');

		$plan_field = $form->addField('xepan\base\DropDown','plan')->validate('required');
		$plan_field->setModel($plan);
		$plan_field->setEmptyText('Please Select');
		
		$form->addField('mobile_no')->validate('required')->set($this->getPhones()[0]);
		$form->addField('email_id')->validate('required')->set($this->getEmails()[0]);

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
		$config->add('xepan\hr\Controller_ACL');
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
}