<?php

 namespace xavoc\ispmanager;
 class page_invoice extends \xepan\commerce\page_salesinvoice{

	public $title='Invoices';
	public $invoice_model = "xavoc\ispmanager\Model_Invoice";

	function init(){
		parent::init();

		$model = $this->crud->getModel();
		$model->addExpression('city')->set(function($m,$q){
			return $q->expr('[0]',[$m->refSQL('contact_id')->fieldQuery('city')]);
		});

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
		});
		$city_field->js('change',$this->filter_form->js()->submit());

	}
}