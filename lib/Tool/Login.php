<?php

namespace xavoc\ispmanager;

class Tool_Login extends \xepan\cms\View_Tool{

	public $options = ['redirect_page'=>'dashboard'];


	function init(){
		parent::init();

		if($this->owner instanceof \AbstractController) return;
		
		$f = $this->add('Form');

		$f->addField('username')->validate('required')->setAttr('placeHolder','enter your mobile number');
		$f->addField('password','password')->validate('required');

		$f->addSubmit('sign in');
		if($f->isSubmitted()){

			$m = $this->add('xavoc/ispmanager/Model_User');
			$m->addCondition('radius_username',$f['username']);
			$m->addCondition('radius_password',$f['password']);
			$m->tryLoadAny();
			if($m->loaded()){
				$f->js()->univ()->redirect($this->app->url($this->options['redirect_page']))->execute();
			}else{
				$f->error('username','wrong credential');
			}
			
		}

	}
}