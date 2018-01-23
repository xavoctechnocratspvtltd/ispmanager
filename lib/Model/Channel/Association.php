<?php

namespace xavoc\ispmanager;

class Model_Channel_Association extends \xepan\base\Model_Table{ 
	
	public $table = "isp_channel_association";
	function init(){
		parent::init();

		$this->hasOne('xavoc\ispmanager\channel','channel_id','unique_name');
		$this->hasOne('xavoc\ispmanager\channel_Plan','plan_id');
		$this->hasOne('xavoc\ispmanager\channel_User','isp_user_id');
		$this->hasOne('xavoc\ispmanager\channel_Invoice','invoice_id');
		$this->hasOne('xavoc\ispmanager\channel_Lead','lead_id');
		$this->hasOne('xavoc\ispmanager\channel_PaymentTransaction','payment_transaction_id');
		
		$this->add('dynamic_model/Controller_AutoCreator');
	}
}