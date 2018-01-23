<?php

namespace xavoc\ispmanager;

class page_channel_paymentcollection extends \xepan\base\Page {
	
	public $title = "channel Payment Collection Management";
		
	function init(){
		parent::init();

		$this->add('View')->set('Payment Collection Management');
		
	}
}