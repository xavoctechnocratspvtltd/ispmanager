<?php

namespace xavoc\ispmanager;


class page_employeepaymentcollection extends \xepan\base\Page {
	
	public $title ="Employee Payment Collection";

	function init(){
		parent::init();

		$payment_tra = $this->add('xavoc\ispmanager\Model_PaymentTransaction');
		$payment_tra->getElement('payment_mode')->caption('Payment Detail');
		// $payment_tra->getElement('is_submitted_to_company')->caption('submitted to company');

		$crud = $this->add('xepan\hr\CRUD',['pass_acl'=>true,'allow_add'=>false,'allow_edit'=>false]);
		$crud->setModel($payment_tra,['employee','contact','created_at','amount','payment_mode','narration','is_submitted_to_company']);

		$crud->grid->addHook('formatRow',function($g){
			$phtml = "";
			if($g->model['payment_mode'] == "Cash"){
				$phtml = "Payment Mode: CASH";
			}elseif($g->model['payment_mode'] == "Cheque"){
				$phtml = "Payment Mode: Cheque"."<br/>";
				$phtml .= "Cheque No: ".$g->model['cheque_no']."<br/>";
				$phtml .= "Cheque Date: ".$g->model['cheque_date']."<br/>";
				$phtml .= "Bank Detail: ".$g->model['bank_detail']."<br/>";

			}elseif($g->model['payment_mode'] == "DD"){
				$phtml = "Payment Mode: DD <br/>";
				$phtml .= "DD No: ".$g->model['dd_no']."<br/>";
				$phtml .= "dd_date: ".$g->model['dd_date']."<br/>";
				$phtml .= "Bank Detail: ".$g->model['bank_detail']."<br/>";
			}

			$g->current_row_html['payment_mode'] = $phtml;
		});

		$received = $crud->grid->addColumn('Button','Received');
		if($pid = $_GET['Received']){
			$pt = $this->add('xavoc\ispmanager\Model_PaymentTransaction');
			$pt->load($pid);
			if(!$pt['is_submitted_to_company']){
				$pt['submitted_by_id'] = $this->app->employee->id;
				$pt['is_submitted_to_company'] = true;
				$pt->save();
				$crud->js()->reload()->execute();
			}else
				$this->js()->univ()->errorMessage("Payment already submitted to company")->execute();

		}
	}
}