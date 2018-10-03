<?php


namespace xavoc\ispmanager;

class page_surrenderrequest extends \xepan\base\Page {
	public $title = "Surrender Request Management";

	function init(){
		parent::init();

		$sr_model = $this->add('xavoc\ispmanager\Model_SurrenderRequest');
		$crud = $this->add('xepan\hr\CRUD',['status_color'=>$sr_model->status_color]);

		$crud->setModel($sr_model,['contact_id','assign_to_id','created_at','device_collection_availibility','narration'],['contact','assign_to','created_at','device_collection_availibility','duration_in_month','narration','status']);
		$crud->grid->addFormatter('contact','Wrap');
		$crud->grid->addFormatter('narration','Wrap');
		$crud->grid->removeAttachment();
		$crud->grid->removeColumn('status');

	}
}