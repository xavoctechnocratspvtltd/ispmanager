<?php

namespace xavoc\ispmanager;

/**
* 
*/
class Model_PaymentTransaction extends \xepan\base\Model_Table{

	public $table = 'isp_payment_transactions';
	function init(){
		parent::init();
		
		$this->hasOne('xepan\base\Contact','contact_id');
		$this->hasOne('xepan\hr\Employee','employee_id')->defaultValue($this->app->employee->id);
		$this->hasOne('xavoc\ispmanager\Invoice','invoice_id');
		$this->hasOne('xavoc\ispmanager\SalesOrder','order_id');
		$this->hasOne('xepan\base\Model_Contact','submitted_by_id');
		
		$this->addField('created_at')->type('datetime')->defaultValue($this->app->now)->system(true);
		$this->addField('payment_mode')->enum(['Cash','Cheque','DD'])->display(['form'=>'xepan\base\DropDownNormal']);
		$this->addField('cheque_no')->type('Number')->defaultValue(0);
		$this->addField('cheque_date')->type('datetime');
		$this->addField('dd_no')->type('Number')->defaultValue(0);
		$this->addField('dd_date')->type('date');
		$this->addField('bank_detail')->type('text');
		$this->addField('amount')->defaultValue(0);

		$this->addField('is_submitted_to_company')->type('boolean')->defaultValue(false);
		$this->addField('submitted_at')->type('date')->system(true);
		$this->addField('narration')->type('text');

		$this->addHook('beforeSave',$this);

		$this->is([
				'contact_id|to_trim|required',
				'employee_id|to_trim|required',
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
				throw $this->Exception(" Bank Details for Cheque must be required",'ValidityCheck')->setField('bank_detail');
		}elseif($this['payment_mode'] === 'DD'){
			if(!$this['dd_no'])
				throw $this->Exception("DD no must be required",'ValidityCheck')->setField('dd_no');
			if(!$this['dd_date'])
				throw $this->Exception("DD Date must be required",'ValidityCheck')->setField('dd_date');
			if(!$this['bank_detail'])
				throw $this->Exception("Bank Details for Demad Draft(DD) must be required",'ValidityCheck')->setField('bank_detail');
		}elseif($this['payment_mode'] === "Cash"){
			if(!$this['amount'])
				throw $this->Exception("amount must be required",'ValidityCheck')->setField('amount');
		}

	}
}