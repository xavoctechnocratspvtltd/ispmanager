<?php

namespace xavoc\ispmanager;

class Model_UserUnclosedSession extends Model_User {
	
	function init(){
		parent::init();

		$this->addExpression('last_rad_acct_id')->set(function($m,$q){
			$mr = $this->add('xavoc\ispmanager\Model_RadAcct');
			$mr->addCondition('username',$m->getElement('radius_username'));
			$mr->setOrder('radacctid','desc');
			$mr->setLimit(1);
			return $q->expr('[0]',[$mr->fieldQuery('radacctid')]);
		});

		$this->addExpression('condition_expire_date')->set(function($m,$q){
			$upt = $m->add('xavoc\ispmanager\Model_UserPlanAndTopup');
			$upt->addCondition('user_id',$m->getElement('id'));
			$upt->addCondition('is_effective',true);
			$upt->setOrder('id','desc');
			$upt->setLimit(1);
			return $q->expr('[0]',[$upt->fieldQuery('expire_date')]);
		});

		$this->addExpression('condition_start_date')->set(function($m,$q){
			$upt = $m->add('xavoc\ispmanager\Model_UserPlanAndTopup');
			$upt->addCondition('user_id',$m->getElement('id'));
			$upt->addCondition('is_effective',true);
			$upt->setOrder('id','desc');
			$upt->setLimit(1);
			return $q->expr('CASE [0]
						       WHEN "days" THEN date_sub([1], INTERVAL [2] DAY)
						       WHEN "hours" THEN date_sub([1], INTERVAL [2] HOUR)
						       WHEN "months" THEN date_sub([1], INTERVAL [2] MONTH)
						       WHEN "years" THEN date_sub([1], INTERVAL [2] YEAR)
						       END',
				[$upt->fieldQuery('data_reset_mode'),$m->getElement('condition_expire_date'),$upt->fieldQuery('data_reset_value')]);
		});

		$this->addExpression('unclosed_session_count')->set(function($m,$q){
			$mr = $this->add('xavoc\ispmanager\Model_RadAcct');
			$mr->addCondition('username',$m->getElement('radius_username'));
			$mr->addCondition('radacctid','<>',$m->getElement('last_rad_acct_id'));
			$mr->addCondition('acctupdatetime','<',$m->getElement('condition_expire_date'));
			$mr->addCondition('acctupdatetime','>=',$m->getElement('condition_start_date'));
			$mr->addCondition('acctstoptime',NULL);
			return $q->expr('[0]',[$mr->count()]);
		});

		$this->addExpression('unclosed_session_data')->set(function($m,$q){
			$mr = $this->add('xavoc\ispmanager\Model_RadAcct');
			$mr->addCondition('username',$m->getElement('radius_username'));
			$mr->addCondition('radacctid','<>',$m->getElement('last_rad_acct_id'));
			$mr->addCondition('acctupdatetime','<',$m->getElement('condition_expire_date'));
			$mr->addCondition('acctupdatetime','>=',$m->getElement('condition_start_date'));
			$mr->addCondition('acctstoptime',NULL);
			return $q->expr('([0]+[1])',[$mr->sum('acctinputoctets'),$mr->sum('acctoutputoctets')]);
		});

		$this->addCondition('unclosed_session_count','>',0);

		$this->add('xavoc\ispmanager\Controller_HumanByte')
			->handleFields(['unclosed_session_data']);
	}
}