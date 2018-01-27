<?php

namespace xavoc\ispmanager;

class Model_Agent extends \xavoc\ispmanager\Model_Channel{

	public $contact_type = "Agent";

	function init(){
		parent::init();

		$this->getElement('permitted_bandwidth')->system(true)->defaultValue(0);
	}
}