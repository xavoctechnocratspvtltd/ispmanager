<?php

namespace xavoc\ispmanager;

class page_channel_invoice extends \xepan\base\Page {
	
	public $title = "Channel Invoice Management";
		
	function init(){
		parent::init();

		$model = $this->add('xavoc\ispmanager\Model_Channel_Invoice');
		$all_fields = $model->getActualFields();
		$all_fields = array_combine($all_fields, $all_fields);

		$crud = $this->add('xepan\hr\CRUD');
		$crud->setModel($model,$all_fields,['document_no','contact','organization_name','net_amount','created_at','channel_name']);
		
		$crud->grid->addFormatter('channel_name','wrap');

		$crud->grid->removeColumn('attachment_icon');
		$filter_form = $crud->grid->addQuickSearch(['document_no','contact','organization_name','net_amount']);
		$crud->grid->addPaginator($ipp=25);

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