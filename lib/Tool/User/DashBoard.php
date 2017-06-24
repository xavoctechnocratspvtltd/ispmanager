<?php

namespace xavoc\ispmanager;


/**
* 
*/
class Tool_User_DashBoard extends \xepan\cms\View_Tool{
	public $options = [
		'login_url'=>'hotspotlogin',
		'nas_ip'=>'192.168.100.1',
		
	];

	function init(){
		parent::init();
		
		if(!$this->app->auth->isLoggedIn()){
			$this->app->redirect($this->app->url($this->options['login_url']));
			return;
		}

		$user = $this->add('xavoc\ispmanager\Model_User');
		$user->loadLoggedIn();
		$plan = $this->add('xavoc\ispmanager\Model_Plan')
			->addCondition('id',$user['plan_id'])
			->tryLoadAny();

		if($_GET['error']){
			$this->add('View')->set($_GET['error']);
			return;
		}
		else{
			$this->add('View')->setHTML("
					<form name='redirect' action='http://".$this->options['nas_ip']."/login'>
						<input type='hidden' name='username' value='".$user['radius_username']."' />
						<input type='hidden' name='password' value='".$user['radius_password']."' />
					</form>
					<script>
						document.redirect.submit();
					</script>
				");
		}

		// echo "string". $user['plan_id'];
		$user = $this->app->auth->model;



		$this->template->set('plan',$plan['name']);
		$this->template->set('username',$user['username']);

	}

	function defaultTemplate(){
		return ['view/user-dashboard'];
	}
}