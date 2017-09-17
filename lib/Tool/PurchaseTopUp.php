<?php

namespace xavoc\ispmanager;


/**
* 
*/
class Tool_PurchaseTopUp extends \xepan\cms\View_Tool{
	public $options = [
						'show_buy_now_btn'=>'true',
						'buy_now_button_name'=>'Buy Now',
						'checkout_page_url_page_url'=>'checkout'

		];

	function init(){
		parent::init();

		if($this->owner instanceof \AbstractController) return;
		
		$this->add('View_Error')->setHTML('I am Purchase TopUp Tool');
	}
}		