<?php

namespace xavoc\ispmanager;

class Model_User_ActivePlan extends \xavoc\ispmanager\Model_UserPlanAndTopup

	function init(){
		parent::init();

		$this->addCondition();
	}

}
