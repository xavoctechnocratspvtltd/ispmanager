<?php

namespace xavoc\ispmanager;

class page_channel_channel extends \xepan\base\Page {
	
	public $title = "channel Management";
		
	function init(){
		parent::init();

		$model = $this->add('xavoc\ispmanager\Model_Channel');
		$model->addExpression('user_password')->set(function($m,$q){
			return $m->refSQL('user_id')->fieldQuery('password');
		});

		$crud = $this->add('xepan\hr\CRUD');
		$form = $crud->form;
		$form->add('xepan\base\Controller_FLC')
			->showLables(true)
			->makePanelsCoppalsible(true)
			->addContentSpot()
			->layout([
					'first_name'=>'channel Details~c1~4',
					'last_name'=>'c2~4',
					'organization'=>'c3~4',
					'address'=>'c11~6',
					'country_id~Country'=>'c12~3',
					'state_id~State'=>'c12~3',
					'city'=>'c13~3',
					'pin_code'=>'c13~3',
					'status'=>'c12~3',
					'email_ids'=>'c14~12~(,) comma seperated mutiple values',
					'contact_nos'=>'c15~12~(,) comma seperated mutiple values',
					'user_name'=>'Login Account Credential~c21~6',
					'password'=>'Login Account Credential~c22~6',
					'permitted_bandwidth'=>'Permitted Bandwidth~c31~4~Permitted Bandwidth ie. 200MB, 2GB etc.'
				]);

		$user_name_field = $form->addField('user_name')->validate('email');
		$password_field = $form->addField('password')->validate('required');
		$email_field = $form->addField('email_ids');
		$contact_field = $form->addField('contact_nos');
		$form->addField('DropDown','status')->setValueList(['Active'=>'Active','InActive'=>'InActive']);
		
		$crud->setModel($model,
				['first_name','last_name','organization','address','country_id','state_id','city','pin_code','permitted_bandwidth','status','action','emails_str','contacts_comma_seperated','user_id'],
				['name','organization','city','permitted_bandwidth','action','emails_str','contacts_comma_seperated','user']
			);
					
		if($crud->isEditing('edit') AND !$crud->form->isSubmitted()){
			$email_field->set(str_replace("<br/>", ",",$crud->model['emails_str']));
			$contact_field->set($crud->model['contacts_comma_seperated']);

			$user_name_field->set($crud->model['user']);
			$password_field->set($crud->model['user_password']);
		}

		// country state field changed
		$state_field = $crud->form->getElement('state_id');
		$state_field->getModel()->addCondition('status','Active');
		if($country_id = $this->app->stickyGET('country_id')){
			$state_field->getModel()->addCondition('country_id',$country_id);
		}		
		$country_field = $crud->form->getElement('country_id');
		$country_field->getModel()->addCondition('status','Active');
		$country_field->js('change',$state_field->js()->reload(null,null,[$this->app->url(null,['cut_object'=>$state_field->name]),'country_id'=>$country_field->js()->val()]));

		if(($crud->isEditing('add') OR $crud->isEditing('edit')) AND $crud->form->isSubmitted()){
			
			$form = $crud->form;
			// $crud->model['user_id'] = $user_model->id;
			$crud->model['status'] = $form['status'];
			$crud->model->save();

			$email_list = explode(",", $form['email_ids']);
			foreach ($email_list as $key => $value){
				if(!$value) continue;

				$emails = $this->add('xepan\base\Model_Contact_Email');
				$emails->addCondition('contact_id',$crud->model->id);
				$emails->addCondition('value',$value);
				$emails->tryLoadAny();

				$emails['head'] = "Official";
				$emails['is_active'] = true;
				$emails['is_valid'] = true;
				$emails->update();
			}

			$phone_list = explode(",", $crud->form['contact_nos']);
			foreach ($phone_list as $key => $value) {
				if(!$value) continue;

				$phone = $this->add('xepan\base\Model_Contact_Phone');
				$phone->addCondition('contact_id',$crud->model->id);
				$phone->addCondition('value',$value);
				$phone->tryLoadAny();

				$phone['head'] = "Official";
				$phone['is_active'] = true;
				$phone['is_valid'] = true;
				$phone->save();
			}

			// update user name and password
			$user_model = $crud->model->updateUser($form['user_name'],$form['password']);
						
		}

		$crud->removeAttachment();
		$crud->grid->addQuickSearch(['name']);
	}
}