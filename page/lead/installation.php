<?php


namespace xavoc\ispmanager;

class page_lead_installation extends \xepan\base\Page{
	public $title = "Lead to be assign for installation";

	function init(){
		parent::init();

		$model = $this->add('xavoc\ispmanager\Model_User');
		$model->addCondition('status','Won');
		
		$crud = $this->add('xepan\hr\CRUD');
		$crud->setModel($model,['name','status']);

	}
}