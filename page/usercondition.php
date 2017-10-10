<?php

namespace xavoc\ispmanager;


class page_usercondition extends \xepan\base\Page {
	
	public $title ="User Current Plan Conditions";

	function init(){
		parent::init();

		$model = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
		
		$grid = $this->add('Grid');
		$grid->setModel($model);

		$grid->addColumn();
	}
}