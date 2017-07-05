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

		// todo check staff is login or not
		$user = $this->app->auth->model;
		$page = $this->app->page."_active";
		// $menu = [
		// 		['key'=>'dashboard','name'=>'Dashboard'],
		// 		['key'=>'setting','name'=>'Settings'],
		// 	];

		// $this->complete_lister = $cl = $this->add('CompleteLister',null,null,['view/usermenubar']);
		// $cl->setSource($menu);
		// $page = $this->app->page;
		// $cl->addHook('formatRow',function($g)use($page){
		// 	if($g->model['key'] == $page)
		// 		$g->current_row_html['active_menu'] = "active";
		// 	else
		// 		$g->current_row_html['active_menu'] = "deactive";
		// });
		// $cl->template->trySet('url', $this->options['plan_url']);
		// $cl->template->trySet('user_name',$user['name']);
		// $cl->template->trySet('user_dp',($user['image']?:"shared/apps/xavoc/ispmanager/templates/img/profile.png"));

		$this->template->trySet('url', $this->options['plan_url']);
		$this->template->trySet('user_name',$user['name']);
		$this->template->trySet($page,'active-nav');
		$this->template->trySet('user_dp',($user['image']?:"shared/apps/xavoc/ispmanager/templates/img/profile.png"));
	}

	function defaultTemplate(){
		return ['view/usermenubar'];
	} 
}