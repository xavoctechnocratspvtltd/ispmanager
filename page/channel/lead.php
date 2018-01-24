<?php

namespace xavoc\ispmanager;

class page_channel_lead extends \xepan\base\Page {
	
	public $title = "Channel Lead Management";
		
	function init(){
		parent::init();

		$model = $this->add('xavoc\ispmanager\Model_Channel_Lead');

		$all_fields = $model->getActualFields();
		$all_fields = array_combine($all_fields, $all_fields);
		$crud = $this->add('xepan\hr\CRUD',['allow_add'=>false]);
		$crud->setModel($model,
			$all_fields,
			['channel_name','name','organization','address','city','source','remark','created_at','emails_str','contacts_str']
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
		$channel_field->js('change',$filter_form->js()->submit());
		
	}
}