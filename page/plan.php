<?php

namespace xavoc\ispmanager;


class page_plan extends \xepan\base\Page {
	
	public $title ="Plan";

	function init(){
		parent::init();

		$plan = $this->add('xavoc\ispmanager\Model_BasicPlan');
		$crud = $this->add('xepan\hr\CRUD');
		if($crud->isEditing()){
			$form = $crud->form;
			$form->setLayout('form/plan');
		}
		$crud->setModel($plan);
		$crud->grid->removeColumn('attachment_icon');

	}
}