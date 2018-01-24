<?php

namespace xavoc\ispmanager;

class Model_Channel_PaymentTransaction extends \xavoc\ispmanager\Model_PaymentTransaction{
	
	function init(){
		parent::init();
		
		$join = $this->join('isp_channel_association.payment_transaction_id');
		$join->addField('channel_id');
		
		$this->addExpression('channel_name',function($m,$q){
			$asso = $m->add('xavoc\ispmanager\Model_Channel_Association');
			$asso->addCondition('payment_transaction_id',$m->getElement('id'));
			return $q->expr('[0]',[$asso->fieldQuery('channel')]);
		});	
	}
}