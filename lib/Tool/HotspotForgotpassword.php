<?php

namespace xavoc\ispmanager;


/**
* 
*/
class Tool_HotspotForgotpassword extends \xepan\cms\View_Tool{
	public $options = [
		'after_password_send_url'=>"hotspot/login",
		'login_page'=>'hotspot/login',
		'registration_page'=>'hotspot/registration'
	];

	function init(){
		parent::init();

		if($this->owner instanceof \AbstractController) return;

		$form = $this->add('Form',null,null,['form/empty']);
		$form->setLayout(['form/hotspot-forgot']);
		$form->layout->add('View',null,'form_title')->set('Forgot Password');
		$form->addField('Number','mobile_no','Enter Registered Mobile No')->validate('required');

		if($this->options['registration_page']){
			$form->layout->template->trySet('registration_url',$this->app->url($this->options['registration_page']));
		}

		if($this->options['login_page']){
			$form->layout->template->trySet('login_url',$this->app->url($this->options['login_page']));
		}	

		$form->addSubmit("Send Password")->addClass('btn btn-success btn-lg text-center btn-block');
		if($form->isSubmitted()){

			$c_s_m = $this->add('xepan\base\Model_ConfigJsonModel',
				[
					'fields'=>[
								'country'=>'DropDown',
								'state'=>'DropDown',
							],
						'config_key'=>'DEFAULT_ISPMANAGER_COUNTRY_STATE_ID',
						'application'=>'ispmanager'
				]);
			$c_s_m->tryLoadAny();

			$user = $this->add('xavoc\ispmanager\Model_User');
			$user->addCondition('radius_username',$form['mobile_no']);
			$user->tryLoadAny();
			if(!$user->loaded()){
				$form->error('mobile_no','you are not a registered user');
			}
			
			$sms_model = $this->add('xepan\base\Model_ConfigJsonModel',
				[
					'fields'=>[
								'forgot_password_sms_content'=>'Text',
							],
						'config_key'=>'ISPMANAGER_EMAIL_SMS_CONTENT',
						'application'=>'ispmanager'
				]);
			$sms_model->tryLoadAny();
			
			// send SMS
			if($this->app->getConfig('send_sms',false)){
				if(!$sms_model['forgot_password_sms_content']) throw new \Exception("Please update Forgot Password sms content");

				$message = $sms_model['forgot_password_sms_content'];
				$temp = $this->add('GiTemplate');
				$temp->loadTemplateFromString($message);
				$msg = $this->add('View',null,null,$temp);
				$msg->template->trySetHTML('otp_number',$user['radius_password']);
				
				$this->add('xepan\communication\Controller_Sms')->sendMessage($form['mobile_no'],$msg->getHtml());
			}

			$this->app->memorize('success_message',"Password send on your registered mobile number");
			$form->js(null)->univ()->redirect($this->app->url($this->options['after_password_send_url']))->execute();
		}

	}
}