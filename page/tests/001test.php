<?php

/**
* description: ATK Page
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xavoc\ispmanager;

class page_tests_001test extends \xepan\base\Page_Tester {
	
	public $title='Test';
	
	public $proper_responses=[''];

    public $user;


    function init(){
        $this->user = $this->add('xavoc\ispmanager\Model_User')->loadBy('name','test_user');
        parent::init();
    }

    function test_a(){
        $this->proper_responses['test_a']=['access'=>true,'dl'=>512,'ul'=>512];
        return $this->user->getCurrent();
    }

}
