<?php


namespace xavoc\ispmanager;

class page_lead_installed extends \xepan\base\Page{
	public $title = "Lead Installed";

	function init(){
		parent::init();

		$model = $this->add('xavoc\ispmanager\Model_User');
		$model->addCondition('status','Installed');	
		
		$crud = $this->add('xepan\hr\CRUD');
		$crud->setModel($model,['name','status']);

	}
}