<?php

namespace xavoc\ispmanager;

/**
* 
*/
class Model_PaymentTransaction extends \xepan\base\Model_Table{

	public $table = 'isp_payment_transactions';
	function init(){
		parent::init();
		
		$this->hasOne('xavoc\ispmanager\User','customer_id');
		$this->hasOne('xepan\hr\Employee','employee_id')->defaultValue($this->app->employee->id);
		$this->hasOne('xavoc\ispmanager\Invoice','invoice_id');
		
		$this->addField('created_at')->type('date')->defaultValue($this->app->today);
		$this->addField('payment_mode')->enum(['Cash','Cheque','DD']);
		$this->addField('cheque_no');
		$this->addField('cheque_date')->type('date');
		$this->addField('dd_no');
		$this->addField('dd_date')->type('date');
		$this->addField('bank_detail')->type('text');
		$this->addField('amount')->defaultValue(0);
	}
}