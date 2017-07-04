<?php

namespace xavoc\ispmanager;

class Tool_Staff_MenuBar extends \xepan\cms\View_Tool{ 
	public $options = ['login_page'=>'login'];

	function init(){
		parent::init();

		// todo check staff is login or not
		$staff = $this->app->auth->model;

		$menu = [
				['key'=>'dashboard','name'=>'Dashboard'],
				['key'=>'user_registration', 'name'=>'Registration'],
				['key'=>'setting','name'=>'Settings'],
			];

		$this->complete_lister = $cl = $this->add('CompleteLister',null,null,['view/staffmenubar']);
		$cl->setSource($menu);
		$page = $this->app->page;
		$cl->addHook('formatRow',function($g)use($page){
			if($g->model['key'] == $page)
				$g->current_row_html['active_menu'] = "active";
			else
				$g->current_row_html['active_menu'] = "deactive";
		});

		$cl->template->trySet('staff_name',$staff['name']);
		$cl->template->trySet('staff_dp',($staff['image']?:"shared/apps/xavoc/ispmanager/templates/img/profile.png"));

	}
}