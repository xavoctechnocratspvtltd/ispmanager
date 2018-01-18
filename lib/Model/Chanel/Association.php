<?php

namespace xavoc\ispmanager;

class Model_Chanel_Association extends \xepan\base\Model_Table{ 
	
	public $table = "isp_chanel_association";
	function init(){
		parent::init();

		$this->hasOne('xavoc\ispmanager\Chanel','chanel_id');
		$this->hasOne('xavoc\ispmanager\Chanel_Plan','plan_id');
		$this->hasOne('xavoc\ispmanager\Chanel_User','user_id');
		$this->hasOne('xavoc\ispmanager\Chanel_Invoice','invoice_id');
		$this->hasOne('xavoc\ispmanager\Chanel_Lead','lead_id');
		$this->hasOne('xavoc\ispmanager\Chanel_PaymentTransaction','payment_transaction_id');
		
		$this->add('dynamic_model/Controller_AutoCreator');
	}
}