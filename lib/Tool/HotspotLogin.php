<?php

namespace xavoc\ispmanager;


/**
* 
*/
class Tool_HotspotLogin extends \xepan\cms\View_Tool{
	public $options = [
						'redirect_url'=>'',
						'registration_url'=>'',
						'button_label'=>'Submit'
	];

	function init(){
		parent::init();

		$form = $this->add('Form',null,null,['form/empty']);
		$form->setLayout(['form/hotspot-login']);
		$form->addField('Number','mobile_no','Mobile No / Username')->validate('required');
		$form->addField('password','password')->validate('required');

		$form->addSubmit($this->options['button_label'])->addClass('btn btn-primary btn-lg text-center btn-block');

		if($this->options['registration_url'])
			$form->layout->template->trySet('registration_url',$this->app->url($this->options['registration_url']));


		if($form->isSubmitted()){
			$form->js(null,$form->js()->univ()->successMessage('Please Wait.....'))->reload()->execute();
		}
	}
}