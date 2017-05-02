<?php

namespace xavoc\ispmanager;


class page_plan extends \xepan\base\Page {
	
	public $title ="Plan";

	function init(){
		parent::init();

		$plan = $this->add('xavoc\ispmanager\Model_Plan');
		$crud = $this->add('xepan\hr\CRUD');
		$crud->setModel($plan);
		$crud->grid->removeColumn('attachment_icon');
		
	}
}