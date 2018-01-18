<?php

namespace xavoc\ispmanager;

class page_chanel_user extends \xepan\base\Page {
	
	public $title = "Chanel User Management";
		
	function init(){
		parent::init();

		$this->add('View')->set('User Management');
		
	}
}