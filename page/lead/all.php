<?php


namespace xavoc\ispmanager;

class page_lead_all extends \Page{
	public $title = "All Lead";

	function init(){
		parent::init();

		$lead_model = $this->add('xavoc\ispmanager\Model_Lead');
		$lead_model->setOrder('id','desc');
		$crud = $this->add('xepan\hr\CRUD');
		$crud->setModel($lead_model,['name']);

		$crud->grid->addPaginator($ipp=50);
		$crud->grid->removeAttachment();
	}
}