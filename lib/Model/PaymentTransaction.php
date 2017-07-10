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
		
		$this->addField('created_at')->type('date')->defaultValue($this->app->today)->system(true);
		$this->addField('payment_mode')->enum(['Cash','Cheque','DD']);
		$this->addField('cheque_no');
		$this->addField('cheque_date')->type('date');
		$this->addField('dd_no');
		$this->addField('dd_date')->type('date');
		$this->addField('bank_detail')->type('text');
		$this->addField('amount')->defaultValue(0);

		$this->addHook('beforeSave',$this);

		$this->is([
				// 'epan_id|required',
				'customer_id|to_trim|required',
				'payment_mode|to_trim|required',
				'amount|to_trim|required',
			]);
	}

	function beforeSave(){
		if($this['payment_mode'] === 'Cheque'){

			if(!$this['cheque_no'])
				throw $this->Exception("Cheque no must be required",'ValidityCheck')->setField('cheque_no');
			if(!$this['cheque_date'])
				throw $this->Exception("Cheque Date must be required",'ValidityCheck')->setField('cheque_date');
			if(!$this['bank_detail'])
				throw $this->Exception("Cheque Date must be required",'ValidityCheck')->setField('bank_detail');
		}
		if($this['payment_mode'] === 'DD'){
			if(!$this['dd_no'])
				throw $this->Exception("DD no must be required",'ValidityCheck')->setField('dd_no');
			if(!$this['dd_date'])
				throw $this->Exception("DD Date must be required",'ValidityCheck')->setField('dd_date');
			if(!$this['bank_detail'])
				throw $this->Exception("Cheque Date must be required",'ValidityCheck')->setField('bank_detail');
		}
	}
}