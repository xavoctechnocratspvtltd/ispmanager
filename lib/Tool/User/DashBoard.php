<?php

namespace xavoc\ispmanager;


/**
* 
*/
class Tool_User_DashBoard extends \xepan\cms\View_Tool{
	public $options = [
		'login_url'=>'hotspotlogin',
		// 'nas_ip'=>'103.89.255.86', // using link-login from MT
		
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
		
		if($ll=$_GET['link-login']){
			$this->app->memorize('link-login',$ll);
			$this->add('View')->setHTML("
					<form name='redirect' action='$ll'>
						<input type='hidden' name='username' value='".$user['radius_username']."' />
						<input type='hidden' name='password' value='".$user['radius_password']."' />
					</form>
					<script>
						document.redirect.submit();
					</script>
				");
		}

		// echo "string". $user['plan_id'];
		// $user = $this->app->auth->model;



		$this->template->set('plan',$plan['name']);
		$this->template->set('username',$this->app->auth->model['username']);

		$list = $user->getCurrentCondition();
		$dw_t_consumed = 0;
		$up_t_consumed = 0;
		$total_data_limit = 0;
		foreach ($list as $key => $condition) {
			$up_t_consumed += $condition['upload_data_consumed'];
			$dw_t_consumed += $condition['download_data_consumed'];
			$total_data_limit += $condition['data_limit'];
		}

		$this->template->trySet('consume_data',$user->byte2human($up_t_consumed + $dw_t_consumed));
		$this->template->trySet('total_data_limit',$user->byte2human($total_data_limit));

	}

	function defaultTemplate(){
		return ['view/user-dashboard'];
	}
}