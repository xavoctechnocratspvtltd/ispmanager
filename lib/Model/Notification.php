<?php

namespace xavoc\ispmanager;

class Model_Notification extends \xepan\communication\Model_Communication {
	
	public $status = ['GeneralNotification'];

	function init(){
		parent::init();

		$this->addCondition('communication_type','GeneralNotification');
		$this->getElement('status')->defaultValue('GeneralNotification');
		$this->getElement('direction')->defaultValue('Out');
	}

}
