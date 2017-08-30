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
		$action = $this->app->stickyGET('action')?:"open";

		// $staff = $this->add('xepan\base\Model_Contact');
		// $staff->loadLoggedIn();
		$this->staff = $this->add('xepan\hr\Model_Employee');
		$this->staff->loadLoggedIn();

		if(!$this->staff->loaded())
			throw new \Exception("staff not found");
		
		switch ($action) {
			case 'open':
				$this->openLead();
				break;
			case 'installation':
				$this->installationLead();
				break;
		}
	}


	function openLead(){

		$lead = $this->add('xavoc\ispmanager\Model_Lead');
		$lead->addCondition('assign_to_id',$this->staff->id);
 		$lead->addCondition('status','Open');
 		
		$crud = $this->add('xepan\hr\CRUD',['allow_add'=>false,'allow_edit'=>false,'allow_del'=>false,'permissive_acl'=>true],null,['grid/mylead']);
		$crud->setModel($lead,['name','organization','','status','created_at','assign_at','emails_str','contacts_str','remark']);

		$crud->grid->addHook('formatRow',function($g){
			$g->current_row_html['created_date'] = date('d M, Y',strtotime($g->model['created_at'])); 
		});

		$crud->grid->addQuickSearch(['name','status','contacts_str','emails_str']);
		$crud->grid->addPaginator(10);
	}

	function installationLead(){

		$lead = $this->add('xavoc\ispmanager\Model_User');
		$lead->addCondition('installation_assign_to_id',$this->staff->id);
 		$lead->addCondition('status','Installation');
 		
		$crud = $this->add('xepan\hr\CRUD',['allow_add'=>false,'allow_edit'=>false,'allow_del'=>false,'permissive_acl'=>true],null,['grid/mylead']);
		$crud->setModel($lead);

		$crud->grid->addQuickSearch(['name','status','contacts_str','emails_str']);
		$crud->grid->addPaginator(10);
	}

}