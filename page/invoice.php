<?php

 namespace xavoc\ispmanager;
 class page_invoice extends \xepan\commerce\page_salesinvoice{

	public $title='Invoices';
	public $invoice_model = "xavoc\ispmanager\Model_Invoice";

	function init(){
		parent::init();
		
	}
}