<?php

namespace xavoc\ispmanager;

class Tool_Staff_MenuBar extends \xepan\cms\View_Tool{ 
	public $options = ['login_page'=>'login'];

	function init(){
		parent::init();

		// todo check staff is login or not
		$staff = $this->app->auth->model;

		// $menu = [
		// 		['key'=>'staff_dashboard','name'=>'Dashboard'],
		// 		['key'=>'staff_registration', 'name'=>'Customer Registration'],
		// 		['key'=>'staff_setting','name'=>'Settings'],
		// 	];
		$page = $this->app->page."_active";


		// $this->complete_lister = $cl = $this->add('CompleteLister',null,null,['view/staffmenubar']);
		// $cl->setSource($menu);
		// // $cl->addHook('formatRow',function($g)use($page){
		// 	if($g->model['key'] == $page)
		// 		$g->current_row_html['active_menu'] = "active";
		// 	else
		// 		$g->current_row_html['active_menu'] = "deactive";
		// });

		$this->template->trySet('staff_name',$staff['name']);
		$this->template->trySet($page,'active-nav');
		$this->template->trySet('staff_dp',($staff['image']?:"shared/apps/xavoc/ispmanager/templates/img/profile.png"));

	}

	function defaultTemplate(){
		return ['view/staffmenubar'];
	}
}