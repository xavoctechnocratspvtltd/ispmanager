<?php

namespace xavoc\ispmanager;


/**
* 
*/
class Tool_HotspotLogin extends \xepan\cms\View_Tool{
	public $options = [
						'after_login_url'=>'hotspotdashboard',
						'registration_url'=>'',
						'button_label'=>'Submit'
	];

	function init(){
		parent::init();

		if($this->app->auth->isLoggedIn() && $_POST['error']){
			$this->app->redirect($this->app->url($this->options['after_login_url'],['error'=>$_POST['error']]));
			return;
		}

		$form = $this->add('Form',null,null,['form/empty']);
		$form->setLayout(['form/hotspot-login']);
		$form->addField('Line','username','Mobile No / Username')->validate('required');
		$form->addField('password','password')->validate('required');

		$form->addSubmit($this->options['button_label'])->addClass('btn btn-primary btn-lg text-center btn-block');

		if($this->options['registration_url']){
			$form->layout->template->trySet('registration_url',$this->app->url($this->options['registration_url']));
		}


		if($form->isSubmitted()){
			$client = $this->add('xavoc\ispmanager\Model_User');
			$client->addCondition('radius_username',$form['username']);
			$client->addCondition('radius_password',$form['password']);
			$client->tryLoadAny();

			if(!$client->loaded() || !$client['user_id']){
				$form->displayError('username','Username or password is not correct');
			}


			$user = $this->app->add('xepan\base\Model_User_Active')
						->load($client['user_id']);

			$this->app->auth->login($user);
			$form->app->redirect($this->app->url($this->options['after_login_url']));
		}
	}
}