<?php

namespace xavoc\ispmanager;

class page_datesmanage extends \xepan\base\Page {
	
	public $title = "Dates Management";
	
	function init(){
		parent::init();

		if(!$this->app->model->isSuperUser()){
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

		$form->addField('DropDown','look_for')->setValueList([0=>'Please Select','start_date'=>'start_date','end_date'=>'end_date','expire_date'=>'expire_date']);
		$form->addField('DatePicker','from_date');
		$form->addField('DatePicker','to_date');

		$form->addSubmit('Search');

		$v= $this->add('View');
		$v1=$v->add('View');
		$crud = $v1->add('xepan\base\CRUD',['allow_add'=>false,'allow_del'=>false]);

		$m = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');

		if($look_for){
			$m->addCondition($look_for,'>=',$from_date);
			$m->addCondition($look_for,'<',$this->app->nextDate($to_date));
			$m->setOrder($look_for.',user_id');
			
			if( $id= $_GET['extend_5_days']){
				$m->load($id);
				$m[$look_for] = date('Y-m-d H:i:s',strtotime("+ 5 days",strtotime($m[$look_for])));
				$m->saveAndUnload();
				$crud->js()->reload()->execute();
			}

			$v->setHTML('<h3>Filter based on <b>'. $look_for.'</b></h3>');

		}else{
			$m->addCondition('id',-1);
		}



		$crud->setModel($m,['user','plan','start_date','end_date','expire_date','is_expired','reset_date']);
		$crud->grid->removeColumn('attachement');
		$crud->grid->addPaginator(100);

		if($form->isSubmitted()){
			if(!$form['look_for']) $form->displayError('look_for','Please specify field');
			if(!$form['from_date']) $form->displayError('from_date','Please specify field');
			if(!$form['to_date']) $form->displayError('to_date','Please specify field');
			
			$v->js()->reload($form->get())->execute();
		}

		$crud->grid->addColumn('Button','extend_5_days', 'Extend '. $look_for.' 5 days');

	}
}