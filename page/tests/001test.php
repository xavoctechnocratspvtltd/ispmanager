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
    public $on_date;

    function init(){
        $this->user = $this->add('xavoc\ispmanager\Model_User')->loadBy('name','test_user');
        $this->user->setPlan('PL-50-M',null,true);
        parent::init();
        if($_GET['testonly']){
            $g = $this->add('Grid');
            $g->add('View',null,'grid_buttons')->set($this->on_date);
            $g->setModel('xavoc\ispmanager\Model_UserPlanAndTopup')->addCondition('user','test_user');
            $g->removeColumn('user');
            $g->removeColumn('plan');
        }
    }

    function test_a(){
        $this->on_date = $this->app->today .' 22:30:00';
        $r = $this->user->getAAADetails($this->on_date);
        $this->proper_responses['test_a']=['data_limit'=>'200000','dl'=>'2mbps','ul'=>'2mbps','access'=>1];
        return ['data_limit'=>$r['result']['data_limit'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'access'=>$r['access']];
    }

}
