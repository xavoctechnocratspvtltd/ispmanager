<?php

namespace xavoc\ispmanager;

class Tool_Register extends \xepan\cms\View_Tool{

	public $options = ['redirect_page'=>'dashboard'];


	function init(){
		parent::init();

		// $f = $this->add('Form');

		// $f->addField('mobilenumber')->validate('required');
		// $f->addSubmit('send otp');
		// $f->reload('')

		// $f->addField('otp')->validate('required');
		// $f->addSubmit('submit');

		// if($f->isSubmitted()){

		// 	$m = $this->add('xavoc/ispmanager/Model_User');
		// 	$m->addCondition('radius_password',$f['otp']);
		// 	$m->tryLoadAny();
		// 	$m->save();

		// 	if($m->loaded()){
		// 		$f->js()->univ()->redirect($this->app->url($this->options['redirect_page']))->execute();
		// 	}else{
		// 		$f->error('password','wrong otp');
		// 	}
			
		// }

	}
}