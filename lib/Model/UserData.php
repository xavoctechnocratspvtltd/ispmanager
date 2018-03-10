<?php

namespace xavoc\ispmanager;

class Model_UserData  extends Model_User {
	
	function init(){
		parent::init();

		$this->addExpression('radius_login_response')->set(function($m,$q){
			return $q->expr('(select checkAuthenticationReadOnly(null,[0]))',[$m->getElement('radius_username')]);
		})->caption('Data Status');

		$this->addExpression('is_online')->set(function($m,$q){
			$t = $m->add('xavoc\ispmanager\Model_RadAcct')
						->addCondition('username',$m->getElement('radius_username'))
						->setOrder('radacctid','desc')
						->setLimit(1);
			return $q->expr('IF([0] is null,1,0)',[$t->fieldQuery('acctstoptime')]);
		})->sortable(true)->type('boolean');
	}
}