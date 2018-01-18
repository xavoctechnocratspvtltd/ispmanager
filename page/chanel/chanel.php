<?php

namespace xavoc\ispmanager;

class page_chanel_chanel extends \xepan\base\Page {
	
	public $title = "Chanel Management";
		
	function init(){
		parent::init();

		$this->add('View')->set('Chanel Management');
		
	}
}