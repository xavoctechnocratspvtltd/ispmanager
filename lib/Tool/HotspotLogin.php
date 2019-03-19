<?php

namespace xavoc\ispmanager;


/**
* 
*/
class Tool_HotspotLogin extends \xepan\cms\View_Tool{
	public $options = [
						'after_login_url'=>'user_dashboard',
						'registration_page'=>'hotspot/registration',
						'forgot_password_page'=>'hotspot/forgotpassword',
						'button_label'=>'Log in',
						'hotspot_base_url'=>'http://isp.prompthotspot.com' // defined as hotpot url in mikrotik, must come from option
	];

	function init(){
		parent::init();

		if($this->owner instanceof \AbstractController) return;
		
		if($_REQUEST['link-login']){
			$this->app->memorize('hotspot-link-login',$_REQUEST['link-login']);
		}

		if($this->app->recall('hotspot-link-login',false)){
			if($this->app->auth->isLoggedIn() && $_POST['error']){
				$this->app->redirect($this->app->url($this->options['after_login_url'],['error'=>$_POST['error']]));
				return;
			}

			if($_GET['logout']){
				$this->app->auth->logout();
				$this->app->forget('isLoggedIn');
				
				$ll = $this->options['hotspot_base_url']."/logout";
				$this->add('View')->setHTML("
						<form name='redirect' action='$ll'>
						</form>
						<script>
							document.redirect.submit();
						</script>
					");
			}
			
		}

		
		if($message = $this->app->recall('success_message')){
			$this->add('View')->set($message)->addClass('alert alert-success');
			$this->app->forget('success_message');
		}

		$form = $this->add('Form',null,null,['form/empty']);
		$form->setLayout(['form/hotspot-login']);
		$form->addField('Line','username','Username')->validate('required');
		$form->addField('password','password')->validate('required');
		$form->addField('hidden','link_login')->set($this->options['hotspot_base_url']."/login");

		$form->addSubmit($this->options['button_label'])->addClass('btn btn-primary btn-lg text-center btn-block');

		if($this->options['registration_page']){
			$form->layout->template->trySet('registration_url',$this->app->url($this->options['registration_page']));
		}else{
			$form->layout->template->tryDel('new_registration_wrapper');
		}

		if($this->options['forgot_password_page']){
			$form->layout->template->trySet('forgot_password_url',$this->app->url($this->options['forgot_password_page']));
		}else{
			$form->layout->template->tryDel('forgot_password_wrapper');
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
			$form->app->redirect($this->app->url($this->options['after_login_url'],['link-login'=>$form['link_login']]));
		}
	}
}