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
			['channel_name','channel_type','name','organization','address','city','source','remark','created_at','emails_str','contacts_str']
		);
		
		$crud->grid->removeColumn('attachment_icon');
		$filter_form = $crud->grid->addQuickSearch(['name']);
		$crud->grid->addPaginator($ipp=50);

		$crud->grid->addHook('formatRow',function($g){
			
			if($g->model['channel_type'])
				$g->current_row_html['channel_name'] = $g->model['channel_name']."<br/>[ ".$g->model['channel_type']." ]";
		});
		$crud->grid->removeColumn('channel_type');

		$channel_field = $filter_form->addField('DropDown','channel_id','Channel');
		$channel_field->setModel('xavoc\ispmanager\Model_Channel');
		$channel_field->setEmptyText('Select Channel');

		$agent_field = $filter_form->addField('DropDown','agent_id','Agent');
		$agent_field->setModel('xavoc\ispmanager\Model_Agent');
		$agent_field->setEmptyText('Select Agent');

		$filter_form->addHook('applyFilter',function($f,$m){
			if($f['channel_id']){
				$m->addCondition('channel_id',$f['channel_id']);
			}
			
			if($f['agent_id']){
				$m->addCondition('channel_id',$f['agent_id']);
			}
		});

		$channel_field->js('change',$filter_form->js()->submit());
		$agent_field->js('change',$filter_form->js()->submit());
		
	}
}