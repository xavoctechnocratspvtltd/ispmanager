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

class page_tests_002test extends page_Tester {
	
	public $title='Test';
	
	public $proper_responses=[''];

    public $user;
    public $on_date;

    function init(){
        $this->user = $this->add('xavoc\ispmanager\Model_User')->loadBy('name','test user');
        $this->user->setPlan('PL-50-M','2017-05-01',true);
        parent::init();
    }


    function test_activated_plan(){
        $this->setDateTime('2017-05-01 22:30:00');
        $r = $this->user->getAAADetails($now=null,$accounting_data=null,$human_redable=true);
        return ['data_limit'=>$r['result']['data_limit'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'access'=>$r['access']];
    }

    function prepare_activated_plan(){
        $this->proper_responses['test_activated_plan']=[
            'data_limit'=>'50.00GB',
            'dl'=>'1.00MB',
            'ul'=>'1.00MB',
            'access'=>1
        ];
    }

    function test_plan_deactivated(){
        $this->setDateTime('2017-06-01 00:01:00'); // next month after disconnect
        $r = $this->user->getAAADetails($now=null,$accounting_data=null,$human_redable=true);
        return ['data_limit'=>$r['result']['data_limit'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'access'=>$r['access']];
    }

    function prepare_plan_deactivated(){
        $this->proper_responses['test_plan_deactivated']=[
            'data_limit'=>'0.00',
            'dl'=>'0.00',
            'ul'=>'0.00',
            'access'=>'0'
        ];
    }

}
