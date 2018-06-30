<?php

namespace xavoc\ispmanager;


class page_employeepaymentcollection extends \xepan\base\Page {
	
	public $title ="Employee Payment Collection";

	function init(){
		parent::init();

		$payment_tra = $this->add('xavoc\ispmanager\Model_PaymentTransaction');
		$payment_tra->getElement('payment_mode')->caption('Payment Detail');
		$payment_tra->setOrder('id','desc');
		$payment_tra->getElement('submitted_by')->caption('Submission Detail');

		$crud = $this->add('xepan\hr\CRUD');
		$crud->setModel($payment_tra,['employee','contact','created_at','amount','payment_mode','narration','is_submitted_to_company','submitted_by','submitted_at','status']);

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

			if($g->model['is_submitted_to_company'])
				$g->current_row_html['submitted_by'] = "Yes,<br/>Submitted to company on ".$g->model['submitted_at']."<br/> Received by: ".$g->model['submitted_by'];
			else
				$g->current_row_html['submitted_by'] = "";
		});

		$crud->grid->removeAttachment();
		$crud->grid->removeColumn('edit');
		$crud->grid->removeColumn('status');
		$crud->grid->removeColumn('is_submitted_to_company');
		$crud->grid->removeColumn('submitted_at');
		$crud->grid->removeColumn('delete');
		$crud->grid->addPaginator($ipp=50);
		$crud->grid->addFormatter('contact','wrap');
		$crud->grid->addFormatter('submitted_by','wrap');

	}
}