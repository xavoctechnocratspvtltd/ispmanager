<?php

namespace xavoc\ispmanager;

/**
* 
*/
class Tool_Staff_PaymentReceived extends \xepan\cms\View_Tool{
	public $options = [];
	function init(){
		parent::init();

		if($this->owner instanceof \AbstractController) return;
		
		$this->add('xavoc\ispmanager\View_PaymentTransaction');
	}
}