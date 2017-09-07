<?php

namespace xavoc\ispmanager;


class page_device extends \xepan\base\Page {
	
	public $title ="Device Management";

	function init(){
		parent::init();

		$crud = $this->add('CRUD');
		$crud->setModel('xavoc\ispmanager\Device');
	}
}