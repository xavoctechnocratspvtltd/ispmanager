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
		
		$user->addExpression('email')->set(function($m,$q){
			$x = $m->add('xepan\base\Model_Contact_Email');
			return $x->addCondition('contact_id',$q->getField('id'))
						->addCondition('is_active',true)
						->addCondition('is_valid',true)
						->setLimit(1)
						->fieldQuery('value');
		});
		$user->addExpression('dob')->set(function($m,$q){
			return $m->add('xepan\base\Model_Contact_Event')
								->addCondition('contact_id',$m->getField('customer_id'))
								->addCondition('head','DOB')
								->fieldQuery('value');
		});

		$user->loadLoggedIn();
		$tabs = $this->add('Tabs');
		$profile_tab = $tabs->addTab('Profile');
		$pass_tab = $tabs->addTab('Change Password');
		$account_tab = $tabs->addTab('My Account');

		$form = $profile_tab->add('Form',null,null,['form/empty']);
		$form->setLayout(['form/user-profile']);
		$form->addField('DatePicker','dob')->set($user['dob']);
		$form->addField('email')->set($user['email']);
		$form->setModel($user,['image_id','image','first_name','last_name','country_id','state_id','city','address','pin_code','dob','emails_str','contacts_str']);
		$form->addField('contact')->set($user['contacts_str']);
		$form->addSubmit("Update")->addClass('btn btn-primary');

		if($form->isSubmitted()){
			$form->update();
			$this->add('xepan\base\Model_Contact_Email')
					->addCondition('contact_id',$user->id)
					->addCondition('head','Official')
					->addCondition('value',$form['email'])
					->addCondition('is_active',true)
					->addCondition('is_valid',1)
					->tryLoadAny()
					->save();

			// $user->addEmail($form['email']);
			$this->add('xepan\base\Model_Contact_Phone')
					->addCondition('contact_id',$user->id)
					->addCondition('head','Official')
					->addCondition('value',$form['contact'])
					->addCondition('is_active',true)
					->addCondition('is_valid',1)
					->tryLoadAny()
					->save();
			// $user->addPhone($form['contact']);
			$this->add('xepan\base\Model_Contact_Event')
					->addCondition('contact_id',$user->id)
					->addCondition('head',"DOB")
					->addCondition('value',$form['dob'])
					->tryLoadAny()
					->save();


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
				$change_pass_form->displayError('new_password','new Password must match');
			
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