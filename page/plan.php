<?php

namespace xavoc\ispmanager;


class page_plan extends \xepan\base\Page {
	
	public $title ="Plan";

	function init(){
		parent::init();


		$this->add('View')->set("plans ");
	}
}