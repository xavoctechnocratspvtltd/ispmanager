<?php

namespace xavoc\ispmanager;

class page_chanel_invoice extends \xepan\base\Page {
	
	public $title = "Chanel Invoice Management";
		
	function init(){
		parent::init();

		$this->add('View')->set('Invoice Management');
		
	}
}