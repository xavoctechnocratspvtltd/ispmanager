<?php

namespace xavoc\ispmanager;


/**
* 
*/
class Tool_Staff_Panel extends \xepan\cms\View_Tool{
	function init(){
		parent::init();

		if(!$this->app->auth->isLoggedIn()){
			$this->app->redirect($this->app->url('staff_login'));
			return;
		}

	}
}