<?php

 namespace xavoc\ispmanager;
 class page_invoice extends \xepan\commerce\page_salesinvoice{

	public $title='Invoices';
	public $invoice_model = "xavoc\ispmanager\Model_Invoice";

	function init(){
		parent::init();

		$this->app->stickyGET('status');
		
		$model = $this->crud->getModel();
		$model->addExpression('city')->set(function($m,$q){
			return $q->expr('[0]',[$m->refSQL('contact_id')->fieldQuery('city')]);
		});
		$model->setOrder('created_at','desc');
		
		$data = $this->app->db->dsql()->expr('SELECT DISTINCT(city) AS city FROM contact')->get();
		$city_list = [];
		foreach ($data as $key => $value) {
			if(!trim($value['city'])) continue;
			$city_list[$value['city']] = $value['city'];
		}

		$city_field = $this->filter_form->addField('DropDown','filter_city');
		$city_field->setValueList($city_list);
		$city_field->setEmptyText('Select City to filter');

		$this->filter_form->addHook('applyFilter',function($f,$m){
			if($f['filter_city']){
				$m->addCondition('city',$f['filter_city']);
			}
			if($f['filter_invoice_send'] == "y"){
				$m->addCondition('qsp_sent_date','<>',0);
			}
			if($f['filter_invoice_send'] == "n"){
				$m->addCondition('qsp_sent_date',0);
			}

		});
		$city_field->js('change',$this->filter_form->js()->submit());

		// filter based on email send or not
		$email_field = $this->filter_form->addField('DropDown','filter_invoice_send');
		$email_field->setValueList(['y'=>'Invoice Sent','n'=>'Not Sent']);
		$email_field->setEmptyText(' Sent Invoice');
		$email_field->js('change',$this->filter_form->js()->submit());
	}
}