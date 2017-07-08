<?php

namespace xavoc\ispmanager;


/**
* 
*/
class Tool_User_MenuBar extends \xepan\cms\View_Tool{
	public $options = [
						'plan_url'=>'plan'
	];
	
	function init(){
		parent::init();

		// todo check User is login or not


		if(!$this->app->auth->isLoggedIn()){
			$this->app->redirect($this->app->url('hostspot_login'));
			return;
		}

		$user = $this->add('xavoc\ispmanager\Model_User');
		$user->loadLoggedIn();

		$page = $this->app->page."_active";
		
		$this->template->trySet('url', $this->options['plan_url']);
		$this->template->trySet('user_name',$user['name']);
		$this->template->trySet($page,'active-nav');
		$this->template->trySet('user_dp',($user['image']?:"shared/apps/xavoc/ispmanager/templates/img/profile.png"));


		$this->on('click','.ispmanager-user-logout-btn',function($js){
			if($ll = $this->app->recall('link-login')){
				$ll = $this->app->memorize('link-login',str_replace('login', 'logout',$ll));	
				$this->add('View')->setHTML("
					<form name='redirect' action='$ll'>
						<input type='hidden' name='username' value='".$user['radius_username']."' />
						<input type='hidden' name='password' value='".$user['radius_password']."' />
					</form>
					<script>
						document.redirect.submit();
					</script>
				");
				// echo "string".$this->app->recall('link-login');
				// "http://xepan-radius.org/?page=user_dashboard&link-login=http%3A%2F%2Fisp.prompt-hotspot.com%2Flogin%3Fdst%3Dhttp%253A%252F%252Fwww.google.co.in%252F"
					
			}
			return $js->redirect($this->app->url('logout'));

		});
	}

	function defaultTemplate(){
		return ['view/usermenubar'];
	} 
}