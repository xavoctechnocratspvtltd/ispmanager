<?php

namespace xavoc\ispmanager;

class page_channel_lead extends \xepan\base\Page {
	
	public $title = "channel Lead Management";
		
	function init(){
		parent::init();

		$this->add('View')->set('Lead Management');
		
	}
}