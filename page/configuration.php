<?php

namespace xavoc\ispmanager;


class page_configuration extends \xepan\base\Page {
	
	public $title ="Configuration";

	function init(){
		parent::init();


		$this->add('View')->set("configuration");
	}
}