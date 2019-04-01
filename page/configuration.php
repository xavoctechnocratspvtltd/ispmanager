<?php

namespace xavoc\ispmanager;


class page_configuration extends \xepan\base\Page {
	
	public $title ="Configuration";

	function page_index(){
		// parent::init();


		$tab = $this->add('Tabs');
		$tab->addTabURL('./location','Location');
		$tab->addTabURL('./content','Email/SMS Content');
		$tab->addTabURL('./hotspotplan','Default HotSpot Plan');
		$tab->addTabURL('./otpexpired','OTP Expired Time');
		$tab->addTabURL('./syslogconfig','SysLog DB Config');
		$tab->addTabURL('./misc','MISC');
		$tab->addTabURL('./mandatory','Mandatory');
		$tab->addTabURL('./caf_layout','CAF Layout');
		$tab->addTabURL('xepan_marketing_leadsource','Lead source');
		$tab->addTabURL('./refund','Refundable Nominal Accounts');
		$tab->addTabURL('./disclaimer','Customer Disclaimer');
		$tab->addTabURL('./cafinvman','CAF Invoice Mandatory');
		
	}

	function page_disclaimer(){
		$config = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'disclaimer_for_customer'=>'xepan\base\RichText',
						],
					'config_key'=>'ISPMANAGER_Refundable_Nominal_Accounts',
					'application'=>'ispmanager'
			]);
		$config->add('xepan\hr\Controller_ACL');
		$config->tryLoadAny();

		$form = $this->add('Form');
		$form->setModel($config);
		$form->addSubmit('save')->addClass('btn btn-primary');
		
		if($form->isSubmitted()){
			$form->save();
			$form->js(null,$form->js()->reload())->univ()->successMessage('Saved Successfully')->execute();
		}	
	}

	function page_refund(){
		$config = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'refundable_nominal_accounts'=>'xepan\base\Multiselect',
						],
					'config_key'=>'ISPMANAGER_Refundable_Nominal_Accounts',
					'application'=>'ispmanager'
			]);
		$config->add('xepan\hr\Controller_ACL');
		$config->tryLoadAny();
		$form = $this->add('Form');
		$form->setModel($config);
		$accounts = $form->getElement('refundable_nominal_accounts');
		$accounts->setModel('xepan\accounts\Ledger');
		$accounts->set(explode(",", $config['refundable_nominal_accounts']));
		$form->addSubmit('save')->addClass('btn btn-primary');
		
		if($form->isSubmitted()){
			$form->save();
			$form->js(null,$form->js()->reload())->univ()->successMessage('Saved Successfully')->execute();
		}

	}

	function page_caf_layout(){
		$config = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'caf_layout'=>'xepan\base\RichText'
					],
					'config_key'=>'ISPMANAGER_MISC',
					'application'=>'ispmanager'
			]);
		$config->tryLoadAny();
		$form = $this->add('Form');
		$form->setModel($config);
		$form->addSubmit('update');
		if($form->isSubmitted()){
			$form->save();
			$form->js(null,$form->js()->reload())
				->univ()->successMessage('Saved Successfully')->execute();
		}
	}

	function page_misc(){
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
		$config->add('xepan\hr\Controller_ACL');
		$config->tryLoadAny();

		$form = $this->add('Form');
		$form->setModel($config);
		$form->getElement('lead_lost_region')->setFieldHint("comma (,) seperated multiple values");
		$form->getElement('attachment_type')->setFieldHint("comma (,) seperated multiple values");
		$form->getElement('invoice_default_status')
				->setValueList(['Draft'=>'Draft','Due'=>'Due']);
		$form->addSubmit('Save');
		if($form->isSubmitted()){
			$form->save();
			$form->js(null,$form->js()->reload())->univ()->successMessage('Saved Successfully')->execute();
		}
	}

	function page_mandatory(){
		$tabs= $this->add('Tabs');
		$conn_tab = $tabs->addTab('Connection Type');
		$comm_tab = $tabs->addTab('Customer Type');

		$model = $this->add('xavoc\ispmanager\Model_Config_Mendatory');
		$model->addCondition('customer_type',null);

		$crud = $conn_tab->add('xepan\base\CRUD');
		$crud->setModel($model,['connection_type','mendatory_fields','mendatory_documents','other_documents']);
		$crud->grid->addFormatter('mendatory_documents','wrap');
		$crud->grid->addFormatter('mendatory_fields','wrap');
		$crud->grid->addFormatter('other_documents','wrap');

		$model = $this->add('xavoc\ispmanager\Model_Config_Mendatory');
		$model->addCondition('connection_type',null);

		$crud = $comm_tab->add('xepan\base\CRUD');
		$crud->setModel($model,['customer_type','mendatory_fields','mendatory_documents','other_documents']);
		$crud->grid->addFormatter('mendatory_documents','wrap');
		$crud->grid->addFormatter('mendatory_fields','wrap');
		$crud->grid->addFormatter('other_documents','wrap');
	}

	function page_location(){
		// $l_tab = $location->add('Tabs');
		// $c_tab  = $l_tab->addTab('Country');
		// $s_tab = $l_tab->addTab('State');
		// $city_tab = $l_tab->addTab('City');


		// $crud = $c_tab->add('xepan\hr\CRUD');
		// $crud->setModel('xepan\base\Country');

		// $crud = $s_tab->add('xepan\hr\CRUD');
		// $crud->setModel('xepan\base\State');

		// $crud = $city_tab->add('xepan\hr\CRUD');
		// $crud->setModel('xavoc\ispmanager\City');

		// Default Country And State 
		$c_s_m = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'country'=>'DropDown',
							'state'=>'DropDown',
						],
					'config_key'=>'DEFAULT_ISPMANAGER_COUNTRY_STATE_ID',
					'application'=>'ispmanager'
			]);
		$c_s_m->add('xepan\hr\Controller_ACL');
		$c_s_m->tryLoadAny();

		// $csm_tab = $location->addTab('SMS Content');
		$form = $this->add('Form');
		$form->setModel($c_s_m);
		$country_field = $form->getElement('country');
		$country_field->setModel('xepan\base\Country');
		$state_field = $form->getElement('state');
		$state_field->setModel('xepan\base\State');
		$form->addSubmit('Save');
		
		if($this->app->stickyGET('country_id'))
			$state_field->getModel()->addCondition('country_id',$_GET['country_id'])->setOrder('name','asc');
		$country_field->js('change',$form->js()->atk4_form('reloadField','state',[$this->app->url(),'country_id'=>$country_field->js()->val()]));

		if($form->isSubmitted()){
			$form->update();
			$form->js(null,$form->js()->reload())->univ()->successMessage('Saved')->execute();
		}
	}


	function page_content(){

		$tab = $this->add('Tabs');
		$otp_tab = $tab->addTab('OTP MSG');

		// Send OTP SMS for Registar user
		$sms_model = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'otp_msg_content'=>'Text',
						],
					'config_key'=>'ISPMANAGER_OTP_CONTENT',
					'application'=>'ispmanager'
			]);
		$sms_model->add('xepan\hr\Controller_ACL');
		$sms_model->tryLoadAny();

		$form = $otp_tab->add('Form');
		$form->setModel($sms_model);
		$form->getElement('otp_msg_content')->setFieldHint('{$otp_number} spot specify in msg content to send  random OTP');
		$form->addSubmit('Save');
		if($form->isSubmitted()){
			$form->update();
			$form->js(null,$form->js()->reload())->univ()->successMessage('Saved')->execute();
		}

		$content_model = $this->add('xavoc\ispmanager\Model_Config_EmailSMS');
		$content_model->tryLoadAny();
		$forgot_tab = $tab->addTab('HotSpot Forgot Password');

		$s_assign_tab = $tab->addTab('Sale Lead Assign');
		$i_assign_tab = $tab->addTab('Installation Lead Assign');
		$new_account_tab = $tab->addTab('New Account');
		$invoice_paid_tab = $tab->addTab('Invoice Paid');
		$renewal_alert_tab = $tab->addTab('Renewal Alert');
		$account_reactivation_tab = $tab->addTab('Account Reactivation');
		$plan_changed_tab = $tab->addTab('Plan Changed');
		$upcoming_invoice_tab = $tab->addTab('Upcoming Invoices');

		$form = $forgot_tab->add('Form');
		$form->setModel($content_model,['forgot_password_sms_content']);
		$form->addSubmit('save')->addClass('btn btn-primary');
		if($form->isSubmitted()){
			$form->save();
			$form->js(null,$form->js()->reload())->univ()->successMessage("saved")->execute();
		}

		$form = $s_assign_tab->add('Form');
		$form->setModel($content_model,['lead_assign_sms_content','lead_assign_email_subject','lead_assign_email_content']);
		$form->addSubmit('save')->addClass('btn btn-primary');
		if($form->isSubmitted()){
			$form->save();
			$form->js(null,$form->js()->reload())->univ()->successMessage("lead assign content updated")->execute();
		}

		// installation lead
		$form = $i_assign_tab->add('Form');
		$form->setModel($content_model,['installation_lead_assign_sms_content','installation_lead_assign_email_subject','installation_lead_assign_email_content']);
		$form->addSubmit('save')->addClass('btn btn-primary');
		if($form->isSubmitted()){
			$form->save();
			$form->js(null,$form->js()->reload())->univ()->successMessage("Installation lead assign content updated")->execute();
		}

		// new account
		$form = $new_account_tab->add('Form');
		$form->setModel($content_model,['new_account_sms_content','new_account_email_subject','new_account_email_content']);
		$form->addSubmit('save')->addClass('btn btn-primary');
		if($form->isSubmitted()){
			$form->save();
			$form->js(null,$form->js()->reload())->univ()->successMessage("New Account content updated")->execute();
		}

		// Invoice Paid
		$form = $invoice_paid_tab->add('Form');
		$form->setModel($content_model,['invoice_paid_sms_content','invoice_paid_email_subject','invoice_paid_email_content']);
		$form->addSubmit('save')->addClass('btn btn-primary');
		if($form->isSubmitted()){
			$form->save();
			$form->js(null,$form->js()->reload())->univ()->successMessage("invoice paid content updated")->execute();
		}

		// Renewal Alert
		$content_model->getElement('renewal_alert_duration')->hint('Renewal alert duration before in days, ie. 0, 5, 10');
		$form = $renewal_alert_tab->add('Form');
		$form->setModel($content_model,['renewal_alert_sms_content','renewal_alert_duration','renewal_alert_email_subject','renewal_alert_email_content']);
		// $form->getElement('renewal_alert_newsletter_id')->setModel('xepan\marketing\Model_Newsletter')->addCondition('status','Approved');
		$form->addSubmit('save')->addClass('btn btn-primary');
		if($form->isSubmitted()){
			$form->save();
			$form->js(null,$form->js()->reload())->univ()->successMessage("renewal alert content updated")->execute();
		}

		//Account Reactivatio
		$form = $account_reactivation_tab->add('Form');
		$form->setModel($content_model,['account_reactivation_sms_content','account_reactivation_email_subject','account_reactivation_email_content']);
		$form->addSubmit('save')->addClass('btn btn-primary');
		if($form->isSubmitted()){
			$form->save();
			$form->js(null,$form->js()->reload())->univ()->successMessage("account reactivation content updated")->execute();
		}

		//Account Reactivatio 
		$form = $plan_changed_tab->add('Form');
		$form->setModel($content_model,['plan_changed_sms_content','plan_changed_email_subject','plan_changed_email_content']);
		$form->addSubmit('save')->addClass('btn btn-primary');
		if($form->isSubmitted()){
			$form->save();
			$form->js(null,$form->js()->reload())->univ()->successMessage("Plan Changed content updated")->execute();
		}

		// upcoming invoices
		$upcoming_model = $this->add('xavoc\ispmanager\Model_Config_UpcomingInvoicesReminder');
		$upcoming_model->tryLoadAny();
		$form = $upcoming_invoice_tab->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->showLables(true)
			->makePanelsCoppalsible(true)
			->layout([
					'days_before_reminder'=>'Send Reminder Before and After End Date~c1~4~comma seperated values ie. 1,2,4,6',
					'days_after_reminder'=>'c2~4~comma seperated values ie. 1,2,4,6',
					'send_on_invoice_status~Send on Last Invoice Status'=>'c23~4',
					'send_reminder'=>'c23~4',
					'sms_content'=>'SMS Content~c3~8',
					'sms_send_from'=>'c4~4',
					'email_subject'=>'Email Content~c5~12',
					'email_body'=>'c5~12',
					'email_send_from'=>'c5~12',
				]);
		$form->setModel($upcoming_model);

		$in_field = $form->getElement('send_on_invoice_status');
		$in_field->setValueList(['Draft'=>'Draft','Submitted'=>'Submitted','Redesign'=>'Redesign','Due'=>'Due','Paid'=>'Paid','Canceled'=>'Canceled']);
		$in_field->enableMultiSelect();
		$in_field->set(explode(",", $upcoming_model['send_on_invoice_status']));

		$form->addSubmit('save')->addClass('btn btn-primary');
		if($form->isSubmitted()){
			$form->save();
			$form->js(null,$form->js()->reload())->univ()->successMessage("Upcoming Invoices content updated")->execute();
		}
	}

	function page_hotspotplan(){

		// Default HotSpot Plan 
		$defalut_plan_model = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'default_hotspot_plan'=>'DropDown',
						],
					'config_key'=>'ISPMANAGER_DEFAULT_HOTSPOT_PLAN',
					'application'=>'ispmanager'
			]);
		$defalut_plan_model->add('xepan\hr\Controller_ACL');
		$defalut_plan_model->tryLoadAny();

		$plan_m = $this->add('xavoc\ispmanager\Model_Plan');
		// if($defalut_plan_model->loaded()){
		// 	// $plan_m->addCondition('id',$defalut_plan_model['default_hotspot_plan']);
		// 	// $plan_m->tryLoadAny();		
		// }
		$form = $this->add('Form');
		$form->setModel($defalut_plan_model);

		$default_plan_field = $form->getElement('default_hotspot_plan');
		$default_plan_field->setModel($plan_m);
		$form->addSubmit('Save');
		if($form->isSubmitted()){
			$form->update();
			$form->js(null,$form->js()->reload())->univ()->successMessage('Saved')->execute();
		}
	}

	function page_otpexpired(){
		//OTP SMS Expired Config
		$otp_m = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'expired_time'=>'Number',
						],
					'config_key'=>'ISPMANAGER_OTP_EXPIRED',
					'application'=>'ispmanager'
			]);
		$otp_m->add('xepan\hr\Controller_ACL');
		$otp_m->tryLoadAny();

		$form = $this->add('Form');
		$form->setModel($otp_m);
		$form->getElement('expired_time')->setFieldHint('Specify Time In Minutes, Example.( 15 )');
		$form->addSubmit('Save');
		if($form->isSubmitted()){
			$form->update();
			$form->js(null,$form->js()->reload())->univ()->successMessage('Saved')->execute();
		}
	}


	function page_syslogconfig(){
		// User SYSLog  Database Configuration
		$db_m = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'host'=>'Line',
							'database_name'=>'Line',
							'database_username'=>'Line',
							'database_password'=>'password',
						],
					'config_key'=>'ISPMANAGER_SYSLOG_DATABASE_CONFIG',
					'application'=>'ispmanager'
			]);
		$db_m->add('xepan\hr\Controller_ACL');
		$db_m->tryLoadAny();

		$form = $this->add('Form');
		$form->setModel($db_m);
		$form->getElement('host')->setFieldHint('Example.( localhost )');
		$form->getElement('database_name')->setFieldHint('Example.( Syslog )');
		$form->getElement('database_username')->setFieldHint('Example.( mysql username )');
		$form->getElement('database_password')->setFieldHint('Example.( mysql password )');
		$form->addSubmit('Save');
		if($form->isSubmitted()){
			$form->update();
			$form->js(null,$form->js()->reload())->univ()->successMessage('Saved')->execute();
		}
	}


	function page_cafinvman(){
		$model = $this->add('xavoc\ispmanager\Model_Config_CAFInvoiceMandatory');
		$crud = $this->add('xepan\base\CRUD');
		$crud->setModel($model,['branch_id'],['branch']);

	}
}