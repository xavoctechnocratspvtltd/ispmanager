<?php


namespace xavoc\ispmanager;


class page_Tester extends \xepan\base\Page_Tester{


	function init(){
		ini_set('memory_limit', '2048M');
        set_time_limit(0);
        gc_enable();
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
		$first_plan_set=true;
		$first_topup_set = true;

		$last_action_date=null;
		$time_consumed = 0;
		foreach ($data as $datetime => $action) {
			
			if(strtotime($last_action_date) != strtotime(date('Y-m-d',strtotime($datetime)))){
				$this->user->cron(date('Y-m-d',strtotime($datetime)));
				if(strtolower(substr($action,0,4)) !=='plan' || !$first_plan_set){
					$this->user->testDebug('Adding 0 byte accounting at mid night',date('Y-m-d',strtotime($datetime)));
					$r = $this->user->getAAADetails(date('Y-m-d 00:00:00',strtotime($datetime)),$accounting_data='0',$time_consumed='0',$human_redable=true);
				}
			}


			$this->setDateTime($datetime);
			switch (strtolower(substr($action,0,4))) {
				case 'auth':
				case 'logi':
				case 'conn':
        			$r = $this->user->getAAADetails($now=null,$accounting_data=null,$time_consumed='0',$human_redable=true);
					break;
				case 'plan':
					$r = $this->user->setPlan(substr($action, 5),$datetime,$first_plan_set?true:false,false,$first_topup_set?true:false);
					$first_plan_set = false;
					$first_topup_set=false;
					break;
				case 'top-':
					$r = $this->user->addTopup(substr($action, 4),$datetime);
					break;
				case 'getd':
					$model = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup')
			            ->addCondition('user_id',$this->user->id);
			        $data=[];
			        foreach ($model as $m) {
			            $data[] =$m->data;    
			        }
			        $r = $this->filterColumns($data,array_keys($this->proper_responses[debug_backtrace()[1]['function']][0]));
					break;
				default:
					$accounting_data = $action;
					$temp = explode("/", $action);
					if(count($temp) == 2){
						$accounting_data = $temp[0];
						$time_consumed = $temp[1]?:0;
					}

        			$r = $this->user->getAAADetails($now=null,$accounting_data,$time_consumed,$human_redable=true);
					break;
			}
			$last_action_date = date('Y-m-d',strtotime($datetime));
		}
		return $r;
	}

	function result($r){
		return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access'],'coa'=>$r['result']['coa'],'time_limit'=>$r['result']['time_limit'],'time_consumed'=>$r['result']['time_consumed']];
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