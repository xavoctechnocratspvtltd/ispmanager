<?php

namespace xavoc\ispmanager;

class page_channel_invoice extends \xepan\base\Page {
	
	public $title = "channel Invoice Management";
		
	function init(){
		parent::init();

		$this->add('View')->set('Invoice Management');
		
	}
}