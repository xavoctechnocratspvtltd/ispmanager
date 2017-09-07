<?php

namespace xavoc\ispmanager;

class Model_Attachment extends \xepan\base\Model_Attachment{ 
	
	public $table_alias = "ispmanager_attachment";

	function init(){
		parent::init();

		$config = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'attachment_type'=>'text'
						],
					'config_key'=>'ISPMANAGER_MISC',
					'application'=>'ispmanager'
			]);
		$config->add('xepan\hr\Controller_ACL');
		$config->tryLoadAny();

		$temp = explode(',', $config['attachment_type']);
		$type = [];
		foreach ($temp as $key => $value) {
			$type[$value] = $value;
		}
		$this->getElement('title')->setValueList($type);
	}
}