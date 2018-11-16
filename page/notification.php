<?php

namespace xavoc\ispmanager;

class page_notification extends \xepan\base\Page {
	
	public $title ="General Notification";

	function init(){
		parent::init();

		$model = $this->add('xavoc\ispmanager\Model_Notification');
		$model->getElement('from_id')->defaultValue($this->app->employee->id);
		$model->getElement('description')->display(array('form'=>'xepan\base\RichText'));
		$crud = $this->add('xepan\hr\CRUD');
		$crud->setModel($model,['to_id','title','description'],['to','title','description','created_at','created_by']);

		$crud->grid->addPaginator(15);
		$crud->grid->addQuickSearch(['to','title']);
		$crud->grid->removeAttachment();
	}
}