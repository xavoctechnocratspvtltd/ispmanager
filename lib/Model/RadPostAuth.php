<?php

namespace xavoc\ispmanager;

class Model_RadPostAuth extends \xepan\base\Model_Table{ 
	public $table = "radpostauth";
	
	function init(){
		parent::init();

		$this->addField('username');
		$this->addField('pass');
		$this->addField('reply');
		$this->addField('authdate');
	}
}