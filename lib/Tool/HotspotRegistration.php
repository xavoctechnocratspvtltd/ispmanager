<?php

namespace xavoc\ispmanager;


/**
* 
*/
class Tool_HotspotRegistration extends \xepan\cms\View_Tool{
	public $options = [];

	function init(){
		parent::init();

		$form = $this->add('Form',null,null,['form/empty']);
		$form->setLayout(['form/hotspot-registration']);
		
		$form->addField('Number','mobile_no','Mobile No')->validate('required');
		$form->addField('Number','otp','OTP');

		$form->addSubmit("Registration")->addClass('btn btn-success btn-lg text-center btn-block');

		if($form->isSubmitted()){
			$form->js(null,$form->js()->univ()->successMessage('Send OTP'))->reload()->execute();
		}
		
	}
}