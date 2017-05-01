<?php

namespace xavoc\ispmanager;

class Model_User extends \xepan\base\Model_Table{ 
	public $table = "isp_user";
	public $acl_type= "ispmanager_user";

	function init(){
		parent::init();

		$this->hasOne('xavoc\ispmanager\Plan','plan_id');

		$this->addField('name');
		$this->addField('password');
		$this->addField('mac_address_cm')->caption('MAC Address CM');
		$this->addField('ip_address_mode_cm')->setValueList(['ip pool'=>'ip pool','static ip'=>'static ip']);
		$this->addField('cm_ip_pool');
		$this->addField('cm_static_ip');
		
		$this->addField('mac_address_cpe')->caption('MAC Address CPE');
		$this->addField('allow_mac_address_cpe_only')->type('boolean');
		$this->addField('ip_address_mode_cpe')->setValueList(['nas pool or dhcp'=>'NAS Pool or DHCP','ip pool'=>'IP pool','static ip'=>'static IP']);
		
		$this->addField('simultaneous_use')->type('Number');
		$this->addField('first_name');
		$this->addField('last_name');
		$this->addField('company_name');
		$this->addField('address');
		$this->addField('city');
		$this->addField('zip');
		$this->addField('country');
		$this->addField('state');
		$this->addField('contact_number');
		$this->addField('email_id');
		$this->addField('vat_id');
		$this->addField('narration')->type('text');
		$this->addField('custom_radius_attributes')->type('text')->caption('Custom RADIUS Attributes');

		$this->addField('is_verified')->type('boolean');
		$this->addField('verified_by')->enum(['email','otp','social']);
		$this->addField('is_active')->type('boolean');
		$this->addField('created_at')->type('datetime')->defaultValue($this->app->now);
		
		$this->add('dynamic_model/Controller_AutoCreator');

		$this->is([
				'name|to_trim|required',
				'password|number|>=4',
				'created_at|to_trim|required'
			]);
	}
}