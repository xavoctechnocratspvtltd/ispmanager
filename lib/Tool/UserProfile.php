<?php

namespace xavoc\ispmanager;


/**
* 
*/
class Tool_UserProfile extends \xepan\cms\View_Tool{
	function init(){
		parent::init();

		$user = $this->add('xavoc\ispmanager\Model_User');
		
		$tab = $this->add('Tabs');
		$profile_tab = $tabs->addTab('Profile');
		$pass_tab = $tabs->addTab('Change Password');
		$account_tab = $tabs->addTab('My Account');
	}


}