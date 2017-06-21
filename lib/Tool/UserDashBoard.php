<?php

namespace xavoc\ispmanager;


/**
* 
*/
class Tool_UserDashBoard extends \xepan\cms\View_Tool{
	public $options = [
		'login_url'=>'hotspotlogin',
		'nas_ip'=>'192.168.100.1',
		'plan_url'=>'plan'
	];

	function init(){
		parent::init();
		
		if(!$this->app->auth->isLoggedIn()){
			throw new \Exception("Error Processing Request", 1);
			
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

		$user = $this->app->auth->model;

		$menu = [
				['key'=>'dashboard','name'=>'Dashboard'],
				['key'=>'registration', 'name'=>'Registration'],
				['key'=>'setting','name'=>'Settings'],
			];

		$this->complete_lister = $cl = $this->add('CompleteLister',null,'menubar',['view/user-dashboard','menubar']);
		$cl->setSource($menu);
		$page = $this->app->page;
		$cl->addHook('formatRow',function($g)use($page,$user){
			if($g->model['key'] == $page)
				$g->current_row_html['active_menu'] = "active";
			else
				$g->current_row_html['active_menu'] = "deactive";

		});

		$cl->template->trySet('username', $user['username']);
		$cl->template->trySet('url', $this->options['plan_url']);
		$cl->template->trySet('user_name',$user['name']);
		$cl->template->trySet('user_dp',($user['image']?:"shared/apps/xavoc/ispmanager/templates/img/profile.png"));


		// $this->add('View')->set('Welcome '. $this->app->auth->model['username']);
		$this->template->set('username',$user['username']);

	}

	function defaultTemplate(){
		return ['view/user-dashboard'];
	}
}