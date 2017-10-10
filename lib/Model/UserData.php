<?php

namespace xavoc\ispmanager;

class Model_UserData  extends Model_User {
	
	function init(){
		parent::init();

		$this->addExpression('radius_login_response')->set(function($m,$q){
			return $q->expr('(select checkAuthenticationReadOnly(null,[0]))',[$m->getElement('radius_username')]);
		})->caption('Data Status');

		$this->addExpression('is_online')->set(function($m,$q){
			return $m->add('xavoc\ispmanager\Model_RadAcct')
						->addCondition('username',$m->getElement('radius_username'))
						->addCondition('acctstoptime',null)
						->count();
		})->sortable(true)->type('boolean');
	}
}