<?php

namespace xavoc\ispmanager;


class page_user extends \xepan\base\Page {
	
	public $title ="User";

	function init(){
		parent::init();


		$this->add('View')->set("hello user ");
	}
}