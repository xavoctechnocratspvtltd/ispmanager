<?php

namespace xavoc\ispmanager;

class page_datesmanage extends \xepan\base\Page {
	
	public $title = "Dates Management";
	
	function page_index(){

		if(!$this->app->auth->model->isSuperUser()){
			$this->add('View')->set('You are not authorised to view the page');
			return;
		}

		$look_for = $this->app->stickyGET('look_for');
		$from_date = $this->app->stickyGET('from_date');
		$to_date = $this->app->stickyGET('to_date');

		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
		->showLables(true)
		// ->makePanelsCoppalsible(true)
		->layout([
				'look_for'=>'Search~c1~3',
				'from_date'=>'c2~3',
				'to_date'=>'c3~3',
				'FormButtons'=>'c4~3'
			]);

		$form->addField('DropDown','look_for')->setValueList([0=>'Please Select','start_date'=>'start_date','end_date'=>'end_date','expire_date'=>'expire_date','reset_date'=>'reset_date']);
		$form->addField('DatePicker','from_date');
		$form->addField('DatePicker','to_date');

		$form->addSubmit('Search');

		$v= $this->add('View');
		$v1=$v->add('View');
		$crud = $v1->add('xepan\base\CRUD',['allow_add'=>false,'allow_del'=>false,'allow_edit'=>false]);

		$m = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');

		$m->addExpression('organization')->set(function($m,$q){
			return $m->refSQL('user_id')->fieldQuery('organization');
		});

		if($look_for){
			if($from_date)
				$m->addCondition($look_for,'>=',$from_date);
			if($to_date)
				$m->addCondition($look_for,'<',$this->app->nextDate($to_date));
			$m->setOrder($look_for.',user_id');
			
			$v->setHTML('<h3>Filter based on <b>'. $look_for.'</b></h3>');

		}else{
			$m->addCondition('id',-1);
		}



		$crud->setModel($m,['user','organization','plan','start_date','end_date','expire_date','is_expired','reset_date']);
		$crud->grid->removeColumn('attachement');
		$crud->grid->removeColumn('organization');
		$crud->grid->addPaginator(100);

		if($form->isSubmitted()){
			if(!$form['look_for']) $form->displayError('look_for','Please specify field');
			
			$v->js()->reload(['look_for'=>$form['look_for'],'from_date'=>$form['from_date']?:0,'to_date'=>$form['to_date']?:0])->execute();
		}

		$crud->grid->addHook('formatRow',function($g){
			$g->current_row_html['user']=$g->model['user'].'<br/> '.$g->model['organization'];
		});

		$crud->grid->js('click')->univ()->frameURL('User Details',[$this->app->url('./details'),'user_plan_condition_id'=>$this->js()->_selectorThis()->data('id')])->_selector('tr');
		$this->app->template->appendHTML('js_include',
                '<style> table tr:hover {cursor: pointer;}'."</style>\n");
	}

	function page_details(){
		$user_plan_condition_id = $this->app->stickyGET('user_plan_condition_id');
		$user_plan_condition = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup')->load($user_plan_condition_id);
		$user = $user_plan_condition->ref('user_id');

		$this->add('xavoc\ispmanager\View_UserDetails',['user'=>$user,'allow_edit'=>true]);
	}
}