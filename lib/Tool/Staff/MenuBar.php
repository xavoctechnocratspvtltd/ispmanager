<?php

namespace xavoc\ispmanager;

class Tool_Staff_MenuBar extends \xepan\cms\View_Tool{ 
	public $options = ['login_page'=>'login'];

	function init(){
		parent::init();

		// todo check staff is login or not
		$staff = $this->app->employee;
		$lead = $this->add('xavoc\ispmanager\Model_Lead');
		$lead->addCondition('assign_to_id',$staff->id);
 		$lead->addCondition('status','Open');
 		$lead_open = $lead->count()->getOne();

 		$lead_badge_html = " ";
 		if($lead_open)
 			$lead_badge_html = '<span class="badge">'.$lead_open.'</span>';
		 	
		$menu = [
				['key'=>'staff_dashboard','name'=>'Dashboard'],
				['key'=>'staff_registration', 'name'=>'Registration'],
				['key'=>'staff_lead','name'=>'Leads '.$lead_badge_html],
				['key'=>'staff_setting','name'=>'Settings'],
				['key'=>'?page=logout','name'=>'Logout']
			];
		$submenu_list = [];

		$page = $this->app->page;
		// $page = $this->app->page."_active";
		$this->complete_lister = $cl = $this->add('CompleteLister',null,null,['view/staffmenubar']);
		$cl->setSource($menu);
		$cl->addHook('formatRow',function($g)use($page,$submenu_list){
			$submenu_html = "";
			$submenu_class = "";

			if(isset($submenu_list[$g->model['key']])){
				$submenu_html = '<ul class="dropdown-menu">';
				foreach ($submenu_list[$g->model['key']] as $s_key => $s_value) {
					$submenu_html .= '<li><a class="dropdown-item" href="'.$s_key.'">'.$s_value.'</a></li>';
				}
				$submenu_html .= '</ul>';
				$submenu_class = "dropdown";

				$g->current_row_html['list'] = '<a href="#" class="nav-link waves-effect waves-light dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">'.$g->model['name'].' <span class="caret"></span></a>';
			}else{
				$g->current_row_html['list'] = '<a class="nav-link waves-effect waves-light" href="'.$g->model['key'].'">'.$g->model['name'].'</a>';
			}

			if($g->model['key'] == $page)
				$g->current_row_html['active_menu'] = "active-nav ".$submenu_class;
			else
				$g->current_row_html['active_menu'] = "deactive-nav ".$submenu_class;
			
			$g->current_row_html['submenu'] = $submenu_html;
		});
		
		$this->js(true)->_selector('.dropdown-toggle')->dropdown();

		$this->template->trySet('staff_dp',($staff['image']?:"shared/apps/xavoc/ispmanager/templates/img/profile.png"));
	}
}