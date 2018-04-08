<?php

namespace xavoc\ispmanager;

/**
* 
*/
class Model_PaymentTransaction extends \xepan\base\Model_Table{

	public $table = 'isp_payment_transactions';
	function init(){
		parent::init();
		
		$this->hasOne('xepan\commerce\Customer','contact_id','unique_name')->display(['form'=>'xepan\base\Basic']);

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
		$this->addHook('afterSave',$this);

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

	function afterSave(){
		if($this['payment_mode'] === "Cash"){
		// Do accounts entry for this customer
			$entry_template =$this->add('xepan\accounts\Model_EntryTemplate')->loadBy('unique_trnasaction_template_code','PARTYCASHRECEIVED');
			$transaction = $entry_template->ref('xepan\accounts\EntryTemplateTransaction')->tryLoadAny(); 

			// $new_transaction->createNewTransaction($transaction['type'],null,date('Y-m-d',strtotime($transaction['transaction_date'])),$transaction['narration'],$transaction['currency'],$transaction['exchange_rate'],null,null,null,$transaction['entry_template_id']);
			$entry_template->executeSave([
					$transaction->id => [
						// 'entry_template_transaction_id'=>$transaction->id,
						'entry_template_id'=>$entry_template->id,
						// 'name'=>$transaction['name'],
						'type'=>$transaction['type'],
						'transaction_date'=>$this->app->now,
						'narration'=> $this['narration'],
						'currency'=>$this->app->epan->default_currency->id,
						'related_id'=>$this->id,
						'related_type'=>'xavoc\ispmanager\Model_PaymentTransaction',
						'exchange_rate'=>1,
						'rows'=>[
									[
										'data-code'=>'cash',
										'currency'=>$this->app->epan->default_currency->id,
										'exchange_rate'=>1,
										'data-side'=>'DR',
										'data-ledger'=> $this->add('xepan\accounts\Model_Ledger')->tryLoadBy('name','Cash Account')->get('id'),
										'data-amount'=> $this['amount']
									],
									[
										'data-code'=>'party',
										'currency'=>$this->app->epan->default_currency->id,
										'exchange_rate'=>1,
										'data-side'=>'CR',
										'data-ledger'=> $this->ref('contact_id')->ledger()->get('id'),
										'data-amount'=>$this['amount']
									]
								]

					]
				]
			);
		}
	}
}