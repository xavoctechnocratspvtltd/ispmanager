<?php

namespace xavoc\ispmanager;

class page_chanel_plan extends \xepan\base\Page {
	
	public $title = "Chanel Plan Management";
		
	function init(){
		parent::init();

		$this->add('View')->set('Plan Management');
		
	}
}