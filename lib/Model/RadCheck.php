<?php

namespace xavoc\ispmanager;

class Model_RadCheck extends \xepan\base\Model_Table{ 
	public $table = "radcheck";
	
	function init(){
		parent::init();

		$this->addField('username');
		$this->addField('attribute');
		$this->addField('op');
		$this->addField('value');
	}
}