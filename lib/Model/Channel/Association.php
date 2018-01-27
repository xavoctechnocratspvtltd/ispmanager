<?php

namespace xavoc\ispmanager;

class Model_Channel_Association extends \xepan\base\Model_Table{ 
	
	public $table = "isp_channel_association";
	public $channel_title_field = "unique_name";
	function init(){
		parent::init();

		$this->hasOne('xepan\base\Contact','channel_id',$this->channel_title_field);
		$this->hasOne('xavoc\ispmanager\Channel_Plan','plan_id');
		$this->hasOne('xavoc\ispmanager\Channel_User','isp_user_id');
		$this->hasOne('xavoc\ispmanager\Channel_Invoice','invoice_id');
		$this->hasOne('xavoc\ispmanager\Channel_Lead','lead_id');
		$this->hasOne('xavoc\ispmanager\Channel_PaymentTransaction','payment_transaction_id');
		
		$this->addExpression('contact_type')->set(function($m,$q){
			return $this->refSql('channel_id')->fieldQuery('type');
		});
		// $this->add('dynamic_model/Controller_AutoCreator');
	}
}