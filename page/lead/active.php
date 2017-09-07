<?php


namespace xavoc\ispmanager;

class page_lead_active extends \xepan\base\Page{
	public $title = "Active Customer";

	function init(){
		parent::init();

		$model = $this->add('xavoc\ispmanager\Model_User');
		$model->addCondition('status','Active');
		$model->setOrder('id','desc');

		$crud = $this->add('xepan\hr\CRUD');
		$crud->setModel($model,['name','status']);
		$crud->grid->addPaginator($ipp=25);
	}
}