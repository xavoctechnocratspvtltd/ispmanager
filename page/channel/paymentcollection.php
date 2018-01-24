<?php

namespace xavoc\ispmanager;

class page_Channel_paymentcollection extends \xepan\base\Page {
	
	public $title = "Channel Payment Collection Management";
		
	function init(){
		parent::init();

		$model = $this->add('xavoc\ispmanager\Model_Channel_PaymentTransaction');
		$all_field = $model->getActualFields();
		$all_field = array_combine($all_field, $all_field);

		$crud = $this->add('xepan\base\CRUD',['allow_add'=>false]);
		$crud->setModel($model,$all_field,['channel_name','contact','payment_mode','amount','narration']);

		$crud->grid->removeColumn('attachment_icon');
		$filter_form = $crud->grid->addQuickSearch(['name']);
		$crud->grid->addPaginator($ipp=50);
		$crud->grid->addFormatter('channel_name','wrap');
		
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

		$channel_field = $filter_form->addField('DropDown','channel_id','Channel');
		$channel_field->setModel('xavoc\ispmanager\Model_Channel');
		$channel_field->setEmptyText('Select Channel');

		$filter_form->addHook('applyFilter',function($f,$m){
			if($f['channel_id']){
				$m->addCondition('channel_id',$f['channel_id']);
			}
		});

		$channel_field->js('change',$filter_form->js()->submit());
		
	}
}