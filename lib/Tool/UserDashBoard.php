<?php

namespace xavoc\ispmanager;


/**
* 
*/
class Tool_UserDashBoard extends \xepan\cms\View_Tool{
	public $options = [
		'login_url'=>'hotspotlogin',
		'nas_ip'=>'192.168.100.1'
	];

	function init(){
		parent::init();
		
		if(!$this->app->auth->isLoggedIn()){
			$this->app->redirect($this->app->url($this->options['login_url']));
			return;
		}

		$user = $this->add('xavoc\ispmanager\Model_User');
		$user->loadLoggedIn();

		if($_GET['error']){
			$this->add('View')->set($_GET['error']);
			return;
		}else{
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


		$this->add('View')->set('Welcome '. $this->app->auth->model['username']);
	}
}