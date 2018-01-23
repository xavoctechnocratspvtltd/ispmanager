<?php

namespace xavoc\ispmanager;

class page_Channel_paymentcollection extends \xepan\base\Page {
	
	public $title = "channel Payment Collection Management";
		
	function init(){
		parent::init();

		$model = $this->add('xavoc\ispmanager\Model_Channel_PaymentTransaction');
		$crud = $this->add('xepan\base\CRUD');
		$crud->setModel($model);
		
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