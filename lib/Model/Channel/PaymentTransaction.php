<?php

namespace xavoc\ispmanager;

class Model_Channel_PaymentTransaction extends \xavoc\ispmanager\Model_PaymentTransaction{
	
	function init(){
		parent::init();
		
		$join = $this->join('isp_channel_association.payment_transaction_id');
		$join->addField('channel_id');
		
	}
}