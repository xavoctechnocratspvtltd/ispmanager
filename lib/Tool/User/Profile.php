<?php

namespace xavoc\ispmanager;


/**
* 
*/
class Tool_User_Profile extends \xepan\cms\View_Tool{
	public $options = ['login_page'=>'index'];

	function init(){
		parent::init();

		$user = $this->add('xavoc\ispmanager\Model_User');
		$user->loadLoggedIn();

		$tabs = $this->add('Tabs');
		$profile_tab = $tabs->addTab('Profile');
		$pass_tab = $tabs->addTab('Change Password');
		$account_tab = $tabs->addTab('My Account');

		$form = $profile_tab->add('Form',null,null,['form/empty']);
		$form->setLayout(['form/user-profile']);
		$form->setModel($user,['first_name','last_name','country_id','state_id','city','address','pin_code']);
		$form->addField('dob');
		$form->addField('email');
		$form->addField('contact');
		$form->addSubmit("Update")->addClass('btn btn-primary');

		if($form->isSubmitted()){
			$form->update();
			$form->js(null,$form->js()->reload())->univ()->successMessage('Profile Updated')->execute();
		}

		$base_user = $this->add('xepan\base\Model_User')->load($this->api->auth->model->id);
		$this->api->auth->addEncryptionHook($base_user);
		
		$change_pass_form = $pass_tab->add('Form');
		$change_pass_form->setLayout(['form/change-password']);
		$change_pass_form->addField('user_name')->set($base_user['username'])->setAttr('disabled',true);
		$change_pass_form->addField('password','old_password')->validate('required');
		$change_pass_form->addField('password','new_password')->validate('required');
		$change_pass_form->addField('password','retype_password')->validate('required');
		$change_pass_form->addSubmit('Update Password')->addClass('btn btn-success');

		if($change_pass_form->isSubmitted()){
			if( $change_pass_form['new_password'] != $change_pass_form['retype_password'])
				$change_pass_form->displayError('new_password','Password must match');
			
			if(!$this->api->auth->verifyCredentials($base_user['username'],$change_pass_form['old_password']))
				$change_pass_form->displayError('old_password','Password not match');

			if($base_user->updatePassword($change_pass_form['new_password'])){
				$user['radius_password'] = $change_pass_form['new_password'];
				$user->save();
				$this->app->auth->logout();
				$this->app->redirect($this->options['login_page']);
			}
			$change_pass_form->js()->univ()->errorMessage('some thing happen wrong')->execute();
		}
		

		$form = $account_tab->add('Form',null,null,['form/empty']);
		$form->setLayout(['form/my-account']);
	}


}