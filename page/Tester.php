<?php


namespace xavoc\ispmanager;


class page_Tester extends \xepan\base\Page_Tester{

	function init(){
		parent::init();
		if($_GET['testonly']){
            $g = $this->add('Grid');
            $g->add('View',null,'grid_buttons')->set($this->on_date);
            $g->setModel('xavoc\ispmanager\Model_UserPlanAndTopup')->addCondition('user','test user');
            $g->removeColumn('user');
            $g->removeColumn('plan');
        }
	}

	function setDateTime($date){
		$this->on_date = $date;
		$this->app->ispnow = $date;
		$this->app->isptoday = date('Y-m-d',strtotime($date));
	}

	function _($data){
		return $this->app->human2byte($data);
	}

}