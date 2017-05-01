<?php

namespace xavoc\ispmanager;


class page_user extends \xepan\base\Page {
	
	public $title ="User";

	function init(){
		parent::init();

		$model = $this->add('xavoc\ispmanager\Model_User');
		$crud = $this->add('xepan\hr\CRUD');
		$crud->setModel($model);
		
	}
}