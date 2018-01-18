<?php

namespace xavoc\ispmanager;

class page_chanel_commission extends \xepan\base\Page {
	
	public $title = "Chanel commission Management";
		
	function init(){
		parent::init();

		$this->add('View')->set('Commission Management');
		
	}
}