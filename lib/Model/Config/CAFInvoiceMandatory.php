<?php

namespace xavoc\ispmanager;

class Model_Config_CAFInvoiceMandatory extends \xepan\base\Model_ConfigJsonModel{
	public $fields = [
				'branch_id'=>'DropDown',
				'branch'=>'DropDown',
			];
	public $config_key = 'ISPMANAGER_CAFInvoiceMandatory';
	public $application = 'ispmanager';

	function init(){
		parent::init();

		$this->getElement('branch_id')->setModel('xepan\base\Model_Branch');
		$this->addHook('afterLoad',function($m){
			$m['branch'] = $this->add('xepan\base\Model_Branch')->load($m['branch_id'])->get('name');
		});

	}
}