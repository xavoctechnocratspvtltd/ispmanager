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

	function process($data){
		$i=0;
		foreach ($data as $datetime => $action) {
			$this->setDateTime($datetime);
			switch (strtolower(substr($action,0,4))) {
				case 'auth':
				case 'logi':
				case 'conn':
        			$r = $this->user->getAAADetails($now=null,$accounting_data=null,$human_redable=true);
					break;
				case 'plan':
					$r = $this->user->setPlan(substr($action, 5),$datetime,$i===0?true:false);
					break;
				default:
        			$r = $this->user->getAAADetails($now=null,$accounting_data=$action,$human_redable=true);
					break;
			}
			$i++;
		}

		return $r;
	}
	
	function defaultTemplate(){
        $g = $this->add('Grid');
		$this->app->debugisp = $this->add('View');
        $g->add('View',null,'grid_buttons')->set($this->on_date);
        $m= $this->add('xavoc\ispmanager\Model_UserPlanAndTopup')->addCondition('user','test user')->setOrder('id');
        $m->getElement('id')->system(false)->visible(true);
        $g->setModel($m);
        $g->removeColumn('user');
        $g->removeColumn('plan');
		return parent::defaultTemplate();
	}

}