<?php


namespace xavoc\ispmanager;

class page_lead_installationassigned extends \xepan\base\Page{
	public $title = "Lead Assigned for installation";

	function init(){
		parent::init();

		$model = $this->add('xavoc\ispmanager\Model_User');
		$model->addCondition('status','Installation');
		
		$crud = $this->add('xepan\hr\CRUD');
		$crud->setModel($model,['name','status']);

	}
}