<?php

namespace xavoc\ispmanager;

class Model_BasicPlan extends \xavoc\ispmanager\Model_Plan{
	function init(){
		parent::init();

		$this->addCondition('is_topup',false);
		$this->addCondition('maintain_data_limit',true);
	}
}