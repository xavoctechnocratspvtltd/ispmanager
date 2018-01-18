<?php

namespace xavoc\ispmanager;

class page_chanel_paymentcollection extends \xepan\base\Page {
	
	public $title = "Chanel Payment Collection Management";
		
	function init(){
		parent::init();

		$this->add('View')->set('Payment Collection Management');
		
	}
}