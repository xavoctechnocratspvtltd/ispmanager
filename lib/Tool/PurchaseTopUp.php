<?php

namespace xavoc\ispmanager;


/**
* 
*/
class Tool_PurchaseTopUp extends \xepan\cms\View_Tool{
	public $options = [];

	function init(){
		parent::init();

		$this->add('H1')->setHTML('I am Purchase TopUp Tool');
	}
}		