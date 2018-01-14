<?php

namespace xavoc\ispmanager;


/**
* 
*/
class Tool_Staff_Profile extends \xepan\cms\View_Tool{
	public $options = [
			'login_page'=>'staff_login'
		];

	function init(){
		parent::init();

		if($this->owner instanceof \AbstractController) return;
		
		$base_user = $this->app->auth->model;

		$tabs = $this->add('Tabs');
		$pass_tab = $tabs->addTab('Change Password');
		$change_pass_form = $pass_tab->add('Form');
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
				$this->app->auth->logout();
				$this->app->redirect($this->options['login_page']);
			}
			$change_pass_form->js()->univ()->errorMessage('some thing happen wrong')->execute();
		}
		

		// $form = $account_tab->add('Form',null,null,['form/empty']);
		// $form->setLayout(['form/my-account']);
	}


}