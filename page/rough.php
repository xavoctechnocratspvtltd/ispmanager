<?php


namespace xavoc\ispmanager;

class page_rough extends \xepan\base\Page {
	
	function init(){
		parent::init();

		$this->user = $this->add('xavoc\ispmanager\Model_User')->loadBy('name','test user');
		$this->user->setPlan('SUNDAY  EXCLUDED 100GB-1m','2017-05-01',true);

	}
}