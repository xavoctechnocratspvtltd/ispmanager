<?php

namespace xavoc\ispmanager;

class page_channel_user extends \xepan\base\Page {
	
	public $title = "channel User Management";
		
	function init(){
		parent::init();

		$this->add('View')->set('User Management');
		
	}
}