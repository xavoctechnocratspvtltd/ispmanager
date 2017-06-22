<?php

namespace xavoc\ispmanager;


/**
* 
*/
class Tool_User_Profile extends \xepan\cms\View_Tool{
	function init(){
		parent::init();

		$user = $this->add('xavoc\ispmanager\Model_User');
		
		$tabs = $this->add('Tabs');
		$profile_tab = $tabs->addTab('Profile');
		$pass_tab = $tabs->addTab('Change Password');
		$account_tab = $tabs->addTab('My Account');

		$form = $profile_tab->add('Form',null,null,['form/empty']);
		$form->setLayout(['form/user-profile']);
		$form->setModel($user,['first_name','last_name','country_id','state_id','city','address','pin_code']);
		$form->addField('dob');
		$form->addField('email_id');
		$form->addField('contact');
		$form->addSubmit("Update",null,'form_buttons');

		if($form->isSubmitted()){
			$form->update();
			$form->js(null,$form->js()->reload())->univ()->successMessage('Profile Updated');
		}

		$form = $pass_tab->add('Form',null,null,['form/empty']);
		$form->setLayout(['form/change-password']);
		$form->setModel($user,['radius_password']);
		$form->addField('retype_password');
		$form->addSubmit("Save Changes",null,'form_buttons');

		if($form->isSubmitted()){
			$form->update();
			$form->js(null,$form->js()->reload())->univ()->successMessage('Successfully Changed');
		}

		$form = $account_tab->add('Form',null,null,['form/empty']);
		$form->setLayout(['form/my-account']);
	}


}