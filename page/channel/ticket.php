<?php

namespace xavoc\ispmanager;

class page_channel_ticket extends \xepan\base\Page {
	
	public $title = "channel Support Ticket";
		
	function init(){
		parent::init();

		$this->add('View')->set('Support Ticket Management');
		
	}
}