<?php

namespace xavoc\ispmanager;

class Model_Employee extends \xepan\hr\Model_Employee{
	function init(){
		parent::init();

		$this->addExpression('mobile_number')->set(function($m,$q){
				$x = $m->add('xepan\base\Model_Contact_Phone');
				return $x->addCondition('contact_id',$q->getField('id'))
						->addCondition('is_active',true)
						->addCondition('is_valid',true)
						->setLimit(1)
						->fieldQuery('value');
			});
		
	}
}