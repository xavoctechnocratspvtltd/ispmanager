<?php

namespace xavoc\ispmanager;

class Model_Attachment extends \xepan\base\Model_Attachment{ 
	
	public $table_alias = "ispmanager_attachment";

	function init(){
		parent::init();

		$m = $this->add('xavoc\ispmanager\Model_Config_Mendatory');
		$type = $m->getFields()['documents'];

		$this->getElement('title')->enum($type);
	}
}