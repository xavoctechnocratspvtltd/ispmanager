<?php

namespace xavoc\ispmanager;

class Model_Channel extends \xepan\base\Model_Contact{

	public $status = ['Active','InActive'];
	public $actions = [
					'Active'=>['view','edit','delete','deactivate','communication','login_credential'],
					'InActive'=>['view','edit','delete','activate','communication','login_credential']
					];

	function init(){
		parent::init();

		$this->addCondition('type','channel');
		
		$channel_j = $this->join('isp_channel.contact_id');
		$channel_j->addField('permitted_bandwidth');

		$this->is(
			['permitted_bandwidth|to_trim|required']
		);
	}

	function page_login_credential($page){

		$user = $this->add('xepan\base\Model_User');
		$user->load($this['user_id']);

		$form = $page->add('Form');
		$form->setModel($user,['username','password','scope','hash','last_login_date','status']);
		$form->addSubmit('Update Credentials');
		if($form->isSubmitted()){
			$form->save();
			$form->js(null,$form->js()->reload())->univ()->successMessage('Password Updated')->execute();
		}

	}
}