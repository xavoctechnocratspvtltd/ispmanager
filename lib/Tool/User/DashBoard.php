<?php

namespace xavoc\ispmanager;

/**
* 
*/
class Tool_User_DashBoard extends \xepan\cms\View_Tool{
	public $options = [
		'login_url'=>'hotspotlogin',
		'hotspot_base_url'=>'http://isp.prompthotspot.com'
		// 'nas_ip'=>'103.89.255.86', // using link-login from MT
		
	];

	function init(){
		parent::init();
		
		if($this->owner instanceof \AbstractController) return;
		
		if(!$this->app->auth->isLoggedIn()){
			$this->app->redirect($this->app->url($this->options['login_url']));
			return;
		}

		$this->user = $user = $this->add('xavoc\ispmanager\Model_User');
		$user->loadLoggedIn();

		$plan = $this->add('xavoc\ispmanager\Model_Plan')
			->addCondition('id',$user['plan_id'])
			->tryLoadAny();

		if($_GET['error']){
			$this->add('View')->set($_GET['error']);
			return;
		}
		
		// if($ll=$_GET['link-login'] OR $ll = $this->app->recall('link-login') ){
		// 	$this->app->memorize('link-login',$ll);
		if($this->app->recall('hotspot-link-login',false)){
			if(!$this->app->recall('isLoggedIn',false)){
				$ll = $this->options['hotspot_base_url']."/login";

				$this->add('View')->setHTML("
						<form name='redirect' action='$ll'>
							<input type='hidden' name='username' value='".$user['radius_username']."' />
							<input type='hidden' name='password' value='".$user['radius_password']."' />
						</form>
						<script>
							document.redirect.submit();
						</script>
					");
				$this->app->memorize('isLoggedIn',true);
			}
		}

		// echo "string". $user['plan_id'];
		// $user = $this->app->auth->model;
		$this->setModel($plan);
		$this->template->set('plan_name',$plan['name']);
		$this->template->setHtml('plan_description',$plan['description']);
		$this->template->set('username',$this->app->auth->model['username']);

		$this->add('xavoc\ispmanager\View_UserData',['isp_user_model'=>$this->user]);
	}

	function defaultTemplate(){
		return ['view/user-dashboard'];
	}

	function dataconsumed(){
		$this->add('xavoc\ispmanager\View_UserDataConsumption');
	}
}