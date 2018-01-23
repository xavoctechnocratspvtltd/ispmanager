<?php

namespace xavoc\ispmanager;

class Model_Channel_User extends \xavoc\ispmanager\Model_User{

	public $status = ['Active','InActive','Installation','Installed','Won'];
	public $actions = [
				'Won'=>['view','active','payment_receive','documents','edit','delete'],
				// 'Installation'=>['view','edit','delete','installed','payment_receive','documents'],
				// 'Installed'=>['view','assign_for_installation','documents','edit','delete','active'],
				'Active'=>['view','AddTopups','CurrentConditions','documents','radius_attributes','deactivate','Reset_Current_Plan_Condition','edit','delete'],
				'InActive'=>['view','edit','delete','active','documents']
			];
	
	function init(){
		parent::init();

		$join = $this->join('isp_channel_association.isp_user_id');
		$join->addField('channel_id');

		$this->getElement('contacts_str')->caption('Contacts');
		$this->getElement('emails_str')->caption('Emails');
		$this->getElement('status')->defaultValue('Won');

	}
}