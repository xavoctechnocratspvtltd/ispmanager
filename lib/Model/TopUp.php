<?php

namespace xavoc\ispmanager;

class Model_TopUp extends \xavoc\ispmanager\Model_Plan{
	function init(){
		parent::init();

		$this->addCondition('is_topup',true);	
	}
}