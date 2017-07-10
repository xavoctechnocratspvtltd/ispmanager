<?php

namespace xavoc\ispmanager;

/**
* 
*/
class View_PaymentTransaction extends \View{
	public $customer_id;
	function init(){
		parent::init();
		$this->customer_id = $this->app->stickyGET('customer_id');
		if(!$this->customer_id){
			$this->add('View')->set($this->customer_id);
			return;
		}

		$inv = $this->add('xavoc\ispmanager\Model_Invoice');
		$inv->addCondition('contact_id',$this->customer_id);
		$inv->addCondition('created_by_id',$this->app->employee->id);
		$inv->setOrder('id','desc');
		$inv->setLimit(1);
		$inv->tryLoadAny();
		$payment_model = $this->add('xavoc\ispmanager\Model_PaymentTransaction');
		$payment_model->addCondition('employee_id',$this->app->auth->model->id);
		if($this->customer_id)
			$payment_model->addCondition('customer_id',$this->customer_id);
		if($inv->loaded())
			$payment_model->addCondition('invoice_id',$inv->id);
		$form = $this->add('Form');
		$form->setModel($payment_model);

		$form->addSubmit('Pay Now')->addClass('btn btn-danger');
		if($form->isSubmitted()){
			$inv['status']= "Paid";
			$inv->save();
			$form->update();
			$form->js()->reload()->execute();
		}
	}
}