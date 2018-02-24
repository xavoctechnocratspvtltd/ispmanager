<?php

namespace xavoc\ispmanager;

class Controller_Reminder extends \AbstractController {
	public $testDebug_object=null;

	function run($date = null,$test_user=null, $testDebug_object=null){
		$model = $this->add('xavoc\ispmanager\Model_RecurringInvoiceItem');
	}

	function testDebug($title, $msg=null,$detail=null){
		if(!$this->testDebug_object) return;
		$this->testDebug_object->testDebug($title,$msg,$detail);
	}
}