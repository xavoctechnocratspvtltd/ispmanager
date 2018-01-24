<?php

namespace xavoc\ispmanager;

class page_channel_user extends \xepan\base\Page {
	
	public $title = "Channel User Management";
		
	function init(){
		parent::init();

		$model = $this->add('xavoc\ispmanager\Model_Channel_User');
		$all_fields = $model->getActualFields();
		$all_fields = array_combine($all_fields, $all_fields);

		$crud = $this->add('xepan\hr\CRUD',['allow_add'=>false]);
		$crud->setModel($model,$all_fields,['user','plan','name','organization','created_at','emails_str','contacts_str','channel_name']);
		$crud->grid->addFormatter('channel_name','wrap');

		$crud->grid->removeColumn('attachment_icon');
		$filter_form = $crud->grid->addQuickSearch(['name']);
		$crud->grid->addPaginator($ipp=50);

		$channel_field = $filter_form->addField('DropDown','filter_channel_id','Channel');
		$channel_field->setModel('xavoc\ispmanager\Model_Channel');
		$channel_field->setEmptyText('Select Channel');

		$filter_form->addHook('applyFilter',function($f,$m){
			if($f['filter_channel_id']){
				$m->addCondition('channel_id',$f['filter_channel_id']);
			}
		});

		$channel_field->js('change',$filter_form->js()->submit());
		
	}
}