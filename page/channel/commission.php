<?php

namespace xavoc\ispmanager;

class page_channel_commission extends \xepan\base\Page {
	
	public $title = "channel commission Management";
		
	function init(){
		parent::init();

		$this->add('View')->set('Commission Management');
		
	}
}