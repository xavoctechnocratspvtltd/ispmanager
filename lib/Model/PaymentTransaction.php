<?php

namespace xavoc\ispmanager;

/**
* 
*/
class Model_PaymentTransaction extends \xepan\base\Model_Table{

	public $table = 'isp_payment_transactions';
	public $acl_type = "PaymentTransaction";
	public $status = ["All"];
	public $actions = [
			"All"=>['view','submitted_to_company','edit','delete']
		];

	function init(){
		parent::init();
		
		$this->hasOne('xepan\commerce\Customer','contact_id','unique_name')->display(['form'=>'xepan\base\DropDown'])->caption('Customer');

		$this->hasOne('xepan\hr\Employee','employee_id')->defaultValue($this->app->employee->id);
		$this->hasOne('xavoc\ispmanager\Invoice','invoice_id','invoice_number');

		$this->hasOne('xavoc\ispmanager\SalesOrder','order_id');
		$this->hasOne('xepan\base\Model_Contact','submitted_by_id');
		$this->hasOne('xepan\base\Model_Branch','branch_id')->defaultValue(@$this->app->branch->id)->system(true);
		$this->hasOne('xepan\base\Model_Contact','created_by_id')->defaultValue($this->app->employee->id)->system(true);

		$this->addField('created_at')->type('datetime')->defaultValue($this->app->now)->system(true);
		$this->addField('payment_mode')->setValueList(['Cash'=>'Cash','Cheque'=>'Cheque','DD'=>'Other/ Online'])->display(['form'=>'xepan\base\DropDownNormal']);
		$this->addField('cheque_no')->type('Number')->defaultValue(0);
		$this->addField('cheque_date')->type('datetime');
		$this->addField('dd_no')->type('Number')->defaultValue(0)->caption('Other Payment Receipt/Transaction No.');
		$this->addField('dd_date')->type('date')->caption('Payment / Transaction on Date');
		$this->addField('bank_detail')->type('text');
		$this->addField('amount')->defaultValue(0);

		$this->addField('is_submitted_to_company')->type('boolean')->defaultValue(false);
		$this->addField('submitted_at')->type('datetime')->system(true);
		$this->addField('narration')->type('text');

		$this->addExpression('status')->set('"All"');
		$this->addExpression('invoice_number')->set(function($m,$q){
			return $m->refSQL('invoice_id')->fieldQuery('invoice_number');
		});
		$this->addExpression('invoice_net_amount')->set(function($m,$q){
			return $m->refSQL('invoice_id')->fieldQuery('net_amount');
		});

		$this->add('xepan\base\Controller_AuditLog');
		$this->addHook('beforeSave',$this);
		// $this->addHook('afterSave',[$this,'paymentReceived']);

		$this->is([
				'contact_id|to_trim|required',
				'employee_id|to_trim|required',
				'payment_mode|to_trim|required',
				'amount|to_trim|required',
				// 'invoice_id|to_trim|required',
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

	function paymentReceived(){

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

			// lodgement here
			if($this['amount'] >= $this['invoice_net_amount']){

				$transaction = $this->add('xepan\accounts\Model_Transaction')
								->addCondition('related_type','xavoc\ispmanager\Model_PaymentTransaction')
								->addCondition('related_id',$this->id)
								->setOrder('id','desc')
								->tryLoadAny()
								;

				$output = $this->add('xepan\commerce\Model_Lodgement')
						->doLodgement(
										[$this['invoice_id']],
										$transaction->id,
										$this['invoice_net_amount'],
										$this->app->epan->default_currency->id,
										1,
										"SalesInvoice"
									);
				if($output[$this['invoice_id']]['status'] == "success"){
					$this->ref('invoice_id')->paid();
				}
			}

		}
	}

	function submitted_to_company(){
		$pt = $this->add('xavoc\ispmanager\Model_PaymentTransaction');
		$pt->load($this->id);
		if(!$pt['is_submitted_to_company']){
			$pt['submitted_by_id'] = $this->app->employee->id;
			$pt['is_submitted_to_company'] = true;
			$pt['submitted_at'] = $this->app->now;
			$pt->save();

			$msg = "Payment ".$this['amount']." submitted to company, collected by ".$this['employee']." for user ".$this['contact'];
			$this->app->employee
				->addActivity($msg, $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null)
				->notifyWhoCan('submitted_to_company','All',$this)
				;
		}else
			return $result_js = $this->app->js()->univ()->errorMessage("Payment already submitted to company and Submitted by ".$this['submitted_by']);
				
	}

}