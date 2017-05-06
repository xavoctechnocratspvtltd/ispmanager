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

class page_tests_001test extends page_Tester {
	
	public $title='Test';
	
	public $proper_responses=[''];

    public $user;
    public $on_date;

    function init(){
        $this->user = $this->add('xavoc\ispmanager\Model_User')->loadBy('name','test user');
        $this->user->setPlan('50GB DATA PLAN','2017-05-01',true);
        parent::init();
    }

    function prepare_a(){
        $this->proper_responses['test_a']=[
            'data_limit'=>$this->_('10gb'),
            'dl'=>$this->_('2mb'),
            'ul'=>$this->_('2mb'),
            'access'=>1
        ];
    }

    function test_a(){
        $this->setDateTime($this->app->today .' 22:30:00');
        $r = $this->user->getAAADetails($now=null,$accounting_data=null,$human_redable=true);
        return ['data_limit'=>$r['result']['data_limit'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'access'=>$r['access']];
    }

}
