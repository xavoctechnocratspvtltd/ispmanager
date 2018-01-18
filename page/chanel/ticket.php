<?php

namespace xavoc\ispmanager;

class page_chanel_ticket extends \xepan\base\Page {
	
	public $title = "Chanel Support Ticket";
		
	function init(){
		parent::init();

		$this->add('View')->set('Support Ticket Management');
		
	}
}