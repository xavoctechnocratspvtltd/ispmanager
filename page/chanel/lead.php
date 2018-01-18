<?php

namespace xavoc\ispmanager;

class page_chanel_lead extends \xepan\base\Page {
	
	public $title = "Chanel Lead Management";
		
	function init(){
		parent::init();

		$this->add('View')->set('Lead Management');
		
	}
}