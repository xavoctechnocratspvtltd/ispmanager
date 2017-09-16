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
		
		$this->template->trySet('plan_url', $this->options['plan_url']);
		$this->template->trySet('user_name',$user['name']);
		$this->template->trySet($page,'active-nav');
		$this->template->trySet('user_dp',($user['image']?:"shared/apps/xavoc/ispmanager/templates/img/profile.png"));


		// $this->on('click','.ispmanager-user-logout-btn',function($js)use($user){
		// 	$ret=[];
		// 	if($ll = $this->app->recall('link-login')){
		// 		$ll = str_replace("/login", "/logout", $ll);
		// 		$ret[]=$this->js(true)->univ()->newWindow($ll);
		// 	}
		// 	// var_dump($ret);
		// 	$ret[]=$js->redirect($this->app->url('logout'));
		// 	return $ret;
			

		// });
	}

	function defaultTemplate(){
		return ['view/usermenubar'];
	} 
}