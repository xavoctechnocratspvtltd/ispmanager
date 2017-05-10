<?php

namespace xavoc\ispmanager;

class page_cron_resetUserPlanAndCondition extends \xepan\base\Page{

	function init(){
		parent::init();

		$this->add('xavoc\ispmanager\Controller_ResetUserPlanAndTopup')->run();
	}

}