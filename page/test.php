<?php

namespace xavoc\ispmanager;


class page_test extends \xepan\base\Page {
	
	public $title ="Test Page";

	function init(){
		parent::init();

		// $this->add('CRUD')->setModel($this->add('xavoc\ispmanager\Model_UserPlanAndTopup'));

		$form = $this->add('Form');
		$form->addField('DropDown', 'user')->setModel($this->add('xavoc\ispmanager\Model_User'));
		$factor = ['daily_uses','monthly_uses','yearly_uses','day','date','time'];
		foreach ($factor as $key => $value) {
			$form->addField($value);
		}

		$form->addSubmit();
		
		$view = $this->add('View')->addClass('main-box');
		if($_GET['current_speed']){
			$view->set('Current Speed = '.$_GET['current_speed']);
		}


		if($form->isSubmitted()){

			$user_id = $form['user'];
			$daily_uses = $form['daily_uses'];
			$monthly_uses = $form['monthly_uses'];
			$yearly_uses = $form['yearly_uses'];
			$day = $form['day'];
			$date = $form['date'];
			$time = $form['time'];

			$user_model = $this->add('xavoc\ispmanager\Model_User')->load($user_id);

			$plan = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup')
						->addCondition('user_id',$user_model['id'])
						->addCondition('is_active',true)
						;
			
			// priority
				// if topup 1000 + count of condition
				// if plan 0 + count of condition
			$plan->addExpression('priority')->set(function($m,$q){
				return $q->expr('IF()');
			});

			$form->js(true,$view->js()->reload(['current_speed'=>$current_speed]))->execute();
		}
	}
}