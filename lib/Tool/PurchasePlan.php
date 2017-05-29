<?php

namespace xavoc\ispmanager;


/**
* 
*/
class Tool_PurchasePlan extends \xepan\cms\View_Tool{
	public $options = [];

	function init(){
		parent::init();

		$this->add('View_Error')->setHTML('I am Purchase Plan Tool');
	}
}		