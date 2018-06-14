<?php

namespace xavoc\ispmanager;

class Model_UserAudit  extends Model_UserData {
	
	function init(){
		parent::init();

		$this->getElement('radius_login_response')->destroy();

		$this->addExpression('active_condition')
					->set($this->refSQL('PlanConditions')
						->addCondition('is_expired','<>',true)
						->count()
					)->caption('Active Condition Count');
		// $this->addExpression('plan_last_condition_record_id')
		// 	->set(
		// 		$this->refSQL('PlanConditions')
		// 			->addCondition('plan_id',$this->getElement('plan_id'))
		// 			->setOrder('id','desc')
		// 			->setLimit(1)
		// 			->fieldQuery('id')
		// 		);

		$this->addExpression('is_last_condition_based_on_user_plan')->set(function($m,$q){
			$cp = $m->refSQL('PlanConditions')
					->setOrder('id','desc')
					->setLimit(1);

			return $q->expr('IF([0] = [1],1,0)',[$this->getElement('plan_id'),$cp->fieldQuery('plan_id')]);
		});

		$this->addExpression('is_last_condition_active')->set(function($m,$q){
			$cp = $m->refSQL('PlanConditions')
					->setOrder('id','desc')
					->setLimit(1);
			return $q->expr('IF([0]=1,0,1)',[$cp->fieldQuery('is_expired')]);
		});

		$this->addExpression('actual_plan_condition')->set(function($m,$q){
			$cm = $m->add('xavoc\ispmanager\Model_Condition')
				->addCondition('plan_id',$m->getElement('plan_id'))
				;
			return $q->expr('[0]',[$cm->count()]);
		})->caption('Actual Plan Condition Count');

		$this->addExpression('not_having_data_reset_value_count')
				->set(
					$this->refSQL('PlanConditions')
						->addCondition('is_expired','<>',true)
						->addCondition([['data_reset_value',null],['data_reset_value',""]])
						->count()
				);
		
	}

	function move_to_first(){
		if($this['is_last_condition_active']){
			return $this->app->js(true)->univ()->errorMessage('no need to move first, already in first position');
		} 
		
		$user_plan = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup')
					->addCondition('is_effective',true)
					->addCondition('is_topup',false)
					->addCondition('is_expired',false)
					->addCondition('user_id',$this->id)
					;
		$done = 0;
		foreach ($user_plan as $condition) {
			$old_id = $condition->id;
			$new_id = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup')->setOrder('id','desc')->tryLoadAny()->id;

			$q = 'Update isp_user_plan_and_topup set id = '.($new_id+1).' where id='.$old_id.";";
			$this->app->db->dsql()->expr($q)->execute();

			$done = 1;
		}

		if($done)
			return $this->app->js(true)->univ()->successMessage('moved to first successfully');
		else
			return $this->app->js(true)->univ()->errorMessage('getting some error, do it manually');

	}

}