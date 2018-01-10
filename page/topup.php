<?php

namespace xavoc\ispmanager;


class page_topup extends \xepan\base\Page {
	
	public $title ="Topup";

	function init(){
		parent::init();

		$plan = $this->add('xavoc\ispmanager\Model_TopUp');

		$plan->addExpression('validity')->set(function($m,$q){
			return $q->expr('CONCAT([0]," ",[1])',[$m->getElement('plan_validity_value'),$m->getElement('qty_unit')]);
		});

		$crud = $this->add('xepan\hr\CRUD');
		if($crud->isEditing()){
			$form = $crud->form;
			$form->setLayout('form/plan');
		}
		
		$crud->setModel($plan,['name','sku','description','sale_price','original_price','status','document_id','id','created_by','updated_by','created_at','updated_at','type','qty_unit_id','qty_unit','renewable_unit','renewable_value','tax_id','tax','plan_validity_value','is_auto_renew','available_in_user_control_panel','is_renewable'],['name','code','sale_price','validity','is_renewable']);
		$crud->grid->removeColumn('attachment_icon');
		$crud->grid->addQuickSearch(['name']);
		$crud->grid->addPaginator($ipp=50);
		
	}
}