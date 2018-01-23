<?php

namespace xavoc\ispmanager;

class page_channel_plan extends \xepan\base\Page {
	
	public $title = "channel Plan Management";
		
	function init(){
		parent::init();

		$model = $this->add('xavoc\ispmanager\Model_Channel_Plan');
		
		$model->addExpression('validity')->set(function($m,$q){
			return $q->expr('CONCAT([0]," ",[1])',[$m->getElement('plan_validity_value'),$m->getElement('qty_unit')]);
		});

		$crud = $this->add('xepan\hr\CRUD');
		if($crud->isEditing()){
			$form = $crud->form;
			$form->setLayout('form/plan');
		}
		$crud->setModel($model,
				['name','sku','description','sale_price','original_price','status','document_id','id','created_by','updated_by','created_at','updated_at','type','qty_unit_id','qty_unit','renewable_unit','renewable_value','tax_id','tax','plan_validity_value','is_auto_renew','available_in_user_control_panel','is_renewable','channel_id'],
				['channel_id','name','code','sale_price','validity','is_renewable']
			);
		
		$crud->grid->removeColumn('attachment_icon');
		$filter_form = $crud->grid->addQuickSearch(['name']);
		$crud->grid->addPaginator($ipp=50);

		$channel_field = $filter_form->addField('DropDown','channel_id','Channel');
		$channel_field->setModel('xavoc\ispmanager\Model_Channel');
		$channel_field->setEmptyText('Select Channel');

		$filter_form->addHook('applyFilter',function($f,$m){
			if($f['channel_id']){
				$m->addCondition('channel_id',$f['channel_id']);
			}
			
		});
	}
}