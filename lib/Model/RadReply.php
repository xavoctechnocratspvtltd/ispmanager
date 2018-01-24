<?php

namespace xavoc\ispmanager;

class Model_RadReply extends \xepan\base\Model_Table{ 
	public $table = "radreply";
	
	function init(){
		parent::init();

		$this->addField('username');
		$this->addField('attribute');
		$this->addField('op');
		$this->addField('value');
	}
}