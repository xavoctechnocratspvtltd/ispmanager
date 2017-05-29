<?php

namespace xavoc\ispmanager;


/**
* 
*/
class Tool_PurchasePlan extends \xepan\cms\View_Tool{
	public $options = [
						'show_buy_now_btn'=>'true',
						'buy_now_button_name'=>'Buy Now',
						'checkout_page_url_page_url'=>'checkout'
	];

	function init(){
		parent::init();

		$this->add('View_Error')->setHTML('I am Purchase Plan Tool');
	}
}		