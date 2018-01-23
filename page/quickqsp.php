<?php


namespace xavoc\ispmanager;

class page_quickqsp extends \xepan\commerce\page_quickqsp{

	public $item_page_url = "?page=xavoc_ispmanager_pos_item";
	public $item_amount_page_url = "?page=xavoc_ispmanager_pos_getamount";
	public $item_detail_page_url = '?page=xavoc_ispmanager_pos_itemcustomfield';
	public $item_shipping_page_url = '?page=xavoc_ispmanager_pos_shippingamount';
	public $customer_page_url = "?page=xavoc_ispmanager_pos_contact";
	public $save_page_url = "?page=xavoc_ispmanager_pos_save";
	
	function init(){
		parent::init();
	}	
}