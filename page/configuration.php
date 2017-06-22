<?php

namespace xavoc\ispmanager;


class page_configuration extends \xepan\base\Page {
	
	public $title ="Configuration";

	function init(){
		parent::init();


		$tab = $this->add('Tabs');
		$location = $tab->addTab('Location');
		$l_tab = $location->add('Tabs');
		$c_tab  = $l_tab->addTab('Country');
		$s_tab = $l_tab->addTab('State');
		$city_tab = $l_tab->addTab('City');

		$crud = $c_tab->add('xepan\hr\CRUD');
		$crud->setModel('xavoc\ispmanager\Country');

		$crud = $s_tab->add('xepan\hr\CRUD');
		$crud->setModel('xavoc\ispmanager\State');

		$crud = $city_tab->add('xepan\hr\CRUD');
		$crud->setModel('xavoc\ispmanager\City');




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