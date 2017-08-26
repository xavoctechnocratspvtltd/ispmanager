<?php

namespace xavoc\ispmanager;

/**
* 
*/
class Tool_Staff_MyLead extends \xepan\cms\View_Tool{
	public $options = [];
	
	
	function init(){
		parent::init();
		
		if(!$this->app->auth->isLoggedIn()){
			$this->app->redirect($this->app->url('staff_login'));
			return;
		}

		// $staff = $this->add('xepan\base\Model_Contact');
		// $staff->loadLoggedIn();
		$staff = $this->add('xepan\hr\Model_Employee');
		$staff->loadLoggedIn();

		if(!$staff->loaded())
			throw new \Exception("staff not found");
			
		$lead = $this->add('xavoc\ispmanager\Model_Lead');
		$lead->addCondition('assign_to_id',$staff->id);
 		$lead->addCondition('status','Open');

		$crud = $this->add('xepan\hr\CRUD',['allow_add'=>false,'allow_edit'=>false,'allow_del'=>false],null,['grid/mylead']);
		$crud->setModel($lead,['name','organization','','status','created_at','assign_at','emails_str','contacts_str','remark']);

		$crud->grid->addHook('formatRow',function($g){
			$g->current_row_html['created_date'] = date('d M, Y',strtotime($g->model['created_at'])); 
		});

		$crud->grid->addQuickSearch(['name','status','contacts_str','emails_str']);
		$crud->grid->addPaginator(10);

	}
}