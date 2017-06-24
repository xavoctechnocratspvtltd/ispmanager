<?php

namespace xavoc\ispmanager;


class page_configuration extends \xepan\base\Page {
	
	public $title ="Configuration";

	function init(){
		parent::init();


		$tab = $this->add('Tabs');
		$location = $tab->addTab('Location');
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
		$form = $location->add('Form');
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

		$sms_tab = $tab->addTab('SMS Content');
		$form = $sms_tab->add('Form');
		$form->setModel($sms_model);
		$form->getElement('otp_msg_content')->setFieldHint('{$otp_number} spot specify in msg content to send  random OTP');
		$form->addSubmit('Save');
		if($form->isSubmitted()){
			$form->update();
			$form->js(null,$form->js()->reload())->univ()->successMessage('Saved')->execute();
		}

		// Default HotSpot Plan 
		$plan_tab = $tab->addTab('Default HotSpot Plan');
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
		if($defalut_plan_model->loaded()){
			$plan_m->addCondition('id',$defalut_plan_model['default_hotspot_plan']);
			$plan_m->tryLoadAny();		
		}
		$form = $plan_tab->add('Form');
		$form->setModel($defalut_plan_model);

		$default_plan_field = $form->getElement('default_hotspot_plan');
		$default_plan_field->setModel($plan_m);
		$form->addSubmit('Save');
		if($form->isSubmitted()){
			$form->update();
			$form->js(null,$form->js()->reload())->univ()->successMessage('Saved')->execute();
		}

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

		$otp_tab = $tab->addTab('OTP Expired Time');
		$form = $otp_tab->add('Form');
		$form->setModel($otp_m);
		$form->getElement('expired_time')->setFieldHint('Specify Time In Minutes, Example.( 15 )');
		$form->addSubmit('Save');
		if($form->isSubmitted()){
			$form->update();
			$form->js(null,$form->js()->reload())->univ()->successMessage('Saved')->execute();
		}
	}
}