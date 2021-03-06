<?php

namespace xavoc\ispmanager;
/**
* 
*/
class Tool_User_Profile extends \xepan\cms\View_Tool{
	public $options = ['login_page'=>'index'];

	function init(){
		parent::init();

		if($this->owner instanceof \AbstractController) return;
		
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
		// $pass_tab = $tabs->addTab('Change Password');
		// $account_tab = $tabs->addTab('My Account');

		$c = $profile_tab->add('Columns')->addClass('row');
		$logo_c = $c->addColumn(5)->addClass('col-md-5');
		$detail_c = $c->addColumn(7)->addClass('col-md-7');

		$dp_view = $logo_c->add('View')->setElement('img')->setAttr(['src'=>$user['image'],'width'=>'100','height'=>'100'])->addClass(' avatar img-circle');
		$logo_c->add('View')->setElement('h3')->set('Update Your Profile Picture');
		$dp_form= $logo_c->add('Form',null,null,['form/empty']);
		$dp_form->addField('xepan\base\Upload','myfile_id')->setModel('xepan/filestore/Image');
		// $dp_form->setLayout(['form/user-profile','logo_wrapper']);
		// $dp_form->setModel($user,['image_id']);
		$dp_form->addSubmit('Update Profile Picture')->addClass('btn btn-success');
		if($dp_form->isSubmitted()){			
			
			$user['image_id'] = $dp_form['myfile_id'];
			$user->save()->reload();
			$dp_form->js(null,[
					$dp_form->js()->univ()->successMessage('Profile Picture Updated'),
					$dp_view->js()->reload(),
					$dp_view->js()->_selector('#navbarDropdownMenuLink img')->attr('src',$user['image'])
				])->reload()->execute();
		}

		$user->getElement('country_id')->getModel()->addCondition('status','Active');
		$user->getElement('state_id')->getModel()->addCondition('status','Active');

		$user_view_html = '
						<div class="row">
							<div class="col-md-2">Username:</div>
							<div class="col-md-9">
								<strong> {$radius_username}</strong>
							</div>
						</div>
						<div class="row">
							<div class="col-md-2">Name:</div>
							<div class="col-md-9">
								<strong> {$first_name} {$last_name}</strong>
							</div>
						</div>
						<hr/>
						<div class="row">
							<div class="col-md-2">Address:</div>
							<div class="col-md-9">
								{$address},<br/>{$city}, {$state}, {$country}, {$pin_code}

							</div>
						</div>
						<hr/>
						<div class="row">
							<div class="col-md-2">Contact Nos:</div>
							<div class="col-md-9">
								{$contact_str}
							</div>
						</div>
						<hr/>
						<div class="row">
							<div class="col-md-2">Email Ids:</div>
							<div class="col-md-9">
								{$email_str}
							</div>
						</div>
						';
		$layout = $this->add('GiTemplate');
		$layout->loadTemplateFromString($user_view_html);

		$user_view = $detail_c->add('View',null,null,$layout);
		$user_view->setHTML($user_view_html);
		$user_view->setModel($user);
		$user_view->template->setHtml('contact_str',$user['contacts_str']);
		$user_view->template->setHtml('email_str',$user['emails_str']);

		// $form = $detail_c->add('Form');
		// $form->setLayout(['form/user-profile','detail_wrapper']);
		// $form->setModel($user,
		// 			['first_name','last_name','country_id','state_id','city','address','pin_code','dob','emails_str','contacts_str']);
		// $form->addField('DatePicker','dob')->set($user['dob']);
		
		// $clist = explode("<br/>",$user['contacts_str']);
		// $elist = explode("<br/>",$user['emails_str']);
		// $form->addField('email')->set(array_slice($elist, -1)[0]);
		// $form->addField('contact')->set(array_slice($clist, -1)[0]);
		
		// $form->addSubmit("Update")->addClass('btn btn-primary');
		// if($form->isSubmitted()){
		// 	if($form['email'] && !filter_var($form['email'], FILTER_VALIDATE_EMAIL)){
  //               $form->error('email','must be valid email address');
  //           }

		// 	$form->update();
		// 	if($form['email']){
		// 		$this->add('xepan\base\Model_Contact_Email')
		// 			->addCondition('contact_id',$user->id)
		// 			->addCondition('head','Official')
		// 			->addCondition('value',$form['email'])
		// 			->addCondition('is_active',true)
		// 			->addCondition('is_valid',1)
		// 			->tryLoadAny()
		// 			->save();
		// 	}

		// 	// $user->addEmail($form['email']);
		// 	if($form['contact']){
		// 		$this->add('xepan\base\Model_Contact_Phone')
		// 				->addCondition('contact_id',$user->id)
		// 				->addCondition('head','Official')
		// 				->addCondition('value',$form['contact'])
		// 				->addCondition('is_active',true)
		// 				->addCondition('is_valid',1)
		// 				->tryLoadAny()
		// 				->save();
		// 	}
		// 	// $user->addPhone($form['contact']);
		// 	$this->add('xepan\base\Model_Contact_Event')
		// 			->addCondition('contact_id',$user->id)
		// 			->addCondition('head',"DOB")
		// 			->addCondition('value',$form['dob'])
		// 			->tryLoadAny()
		// 			->save();


		// 	$form->js(null,$form->js()->reload())->univ()->successMessage('Profile Updated')->execute();
		// }

		// $base_user = $this->add('xepan\base\Model_User')->load($this->api->auth->model->id);
		// $this->api->auth->addEncryptionHook($base_user);
		
		// $change_pass_form = $pass_tab->add('Form');
		// $change_pass_form->setLayout(['form/change-password']);
		// $change_pass_form->addField('user_name')->set($base_user['username'])->setAttr('disabled',true);
		// $change_pass_form->addField('password','old_password')->validate('required');
		// $change_pass_form->addField('password','new_password')->validate('required');
		// $change_pass_form->addField('password','retype_password')->validate('required');
		// $change_pass_form->addSubmit('Update Password')->addClass('btn btn-success');

		// if($change_pass_form->isSubmitted()){
		// 	if( $change_pass_form['new_password'] != $change_pass_form['retype_password'])
		// 		$change_pass_form->displayError('new_password','Password must match');
			
		// 	if(!$this->api->auth->verifyCredentials($base_user['username'],$change_pass_form['old_password']))
		// 		$change_pass_form->displayError('old_password','Password not match');

		// 	if($base_user->updatePassword($change_pass_form['new_password'])){
		// 		$user['radius_password'] = $change_pass_form['new_password'];
		// 		$user->save();

		// 		$user->updateNASCredential();
		// 		$this->app->auth->logout();
		// 		$this->app->redirect($this->options['login_page']);
		// 	}
		// 	$change_pass_form->js()->univ()->errorMessage('some thing happen wrong')->execute();
		// }
		

		// $form = $account_tab->add('Form',null,null,['form/empty']);
		// $form->setLayout(['form/my-account']);
	}


}