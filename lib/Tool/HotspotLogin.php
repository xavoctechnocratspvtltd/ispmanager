<?php

namespace xavoc\ispmanager;


/**
* 
*/
class Tool_HotspotLogin extends \xepan\cms\View_Tool{
	public $options = [
						'redirect_url'=>'',
						'after_login_url'=>'hotspotdashboard',
						'registration_url'=>'',
						'button_label'=>'Submit'
	];

	function init(){
		parent::init();

		$form = $this->add('Form',null,null,['form/empty']);
		$form->setLayout(['form/hotspot-login']);
		$form->addField('Line','username','Mobile No / Username')->validate('required');
		$form->addField('password','password')->validate('required');

		$form->addSubmit($this->options['button_label'])->addClass('btn btn-primary btn-lg text-center btn-block');

		if($this->options['registration_url'])
			$form->layout->template->trySet('registration_url',$this->app->url($this->options['registration_url']));


		if($form->isSubmitted()){
			$user = $this->add('xavoc\ispmanager\Model_User');
			$user->addCondition('radius_username',$form['username']);
			$user->addCondition('radius_password',$form['password']);
			$user->tryLoadAny();

			if(!$user->loaded()){
				$form->displayError('username','Username or password is not correct');
			}

			$form->app->redirect($this->app->url($this->options['after_login_url']));
		}
	}
}