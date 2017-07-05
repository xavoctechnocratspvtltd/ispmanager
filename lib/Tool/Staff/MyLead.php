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

		$staff = $this->add('xepan\base\Model_Contact');
		$staff->loadLoggedIn();
		

		$lead = $this->add('xavoc\ispmanager\Model_Lead');
		$lead->addCondition('assign_to_id',$staff->id);
		$crud = $this->add('xepan\base\CRUD',['allow_add'=>false,'allow_edit'=>false,'allow_del'=>false],null,['grid/mylead']);
		$crud->setModel($lead);

		$crud->grid->addHook('formatRow',function($g){
			$g->current_row_html['created_date'] = date('d M, Y',strtotime($g->model['created_at'])); 
		});

		$crud->grid->addQuickSearch(['name','status','contacts_str','emails_str']);
		// // $grid->addPaginator(10);
		// $p = $grid->add('xepan\base\Paginator');

		// $p->setRowsPerPage(10);

	}
}