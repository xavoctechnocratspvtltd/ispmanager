<?php


namespace xavoc\ispmanager;

class page_client extends \xepan\base\Page {
	
	function init(){
		parent::init();

		$model = $this->add('xavoc\ispmanager\Model_Client');

		$vp = $this->add('VirtualPage');
		$vp->set(function($page){
			$client = $page->add('xavoc\ispmanager\Model_Client');
			$client->addCondition('status','Active');
			$config = "";
			foreach ($client as $model) {
				$config .= $model->getConfig();
			}

			$page->add('View')
				->setElement('textarea')
				->setAttr('rows',20)
				->set($config);
		});

		$crud = $this->add('xepan\hr\CRUD');
		$crud->setModel($model,['name','ipaddr','secret'],['name','ipaddr','secret','status']);

		$crud->grid->add('Button',null,'grid_buttons')
					->set('Generate Config')
					->addClass('btn btn-primary')
					->js('click')
					->univ()
					->frameURL($vp->getURL())
				;

		$crud->grid->removeColumn('status');
		$crud->grid->removeColumn('attachment_icon');
	}
}