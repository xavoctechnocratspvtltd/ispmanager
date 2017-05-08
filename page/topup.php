<?php

namespace xavoc\ispmanager;


class page_topup extends \xepan\base\Page {
	
	public $title ="Topup";

	function init(){
		parent::init();

		$plan = $this->add('xavoc\ispmanager\Model_TopUp');
		$crud = $this->add('xepan\hr\CRUD');
		if($crud->isEditing()){
			$form = $crud->form;
			$form->setLayout('form/topup');
		}
		$crud->setModel($plan);
		$crud->grid->removeColumn('attachment_icon');

	}
}