<?php


namespace xavoc\ispmanager;


class page_Tester extends \xepan\base\Page_Tester{


	function init(){
		parent::init();
		
	}

	function setDateTime($date){
		$this->on_date = $date;
		$this->app->ispnow = $date;
		$this->app->isptoday = date('Y-m-d',strtotime($date));
	}

	function _($data){
		return $this->app->human2byte($data);
	}

	function filterColumns($data,$fields){
		foreach ($data as &$datum) {
			foreach ($datum as $field => $value) {
				if(!in_array($field, $fields)) unset($datum[$field]);
			}
		}
		return $data;
	}
	
	function defaultTemplate(){
		if($_GET['testonly']){
            $g = $this->add('Grid');
			$this->app->debugisp = $this->add('View');
            $g->add('View',null,'grid_buttons')->set($this->on_date);
            $m= $this->add('xavoc\ispmanager\Model_UserPlanAndTopup')->addCondition('user','test user')->setOrder('id');
            $m->getElement('id')->system(false)->visible(true);
            $g->setModel($m);
            $g->removeColumn('user');
            $g->removeColumn('plan');
        }
		return parent::defaultTemplate();
	}

}