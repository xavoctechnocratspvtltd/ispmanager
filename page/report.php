<?php

namespace xavoc\ispmanager;

class page_report extends \xepan\base\Page {
	
	public $title ="Reports";

	function page_index(){
		$tab = $this->add('Tabs');
		$tab->addTabUrl('./user','User');
	}

	function page_user(){

		$date = $this->app->today;
		$this->add('View')->set('User Under FUP on date: '.$date);

		$m = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
		$m->addExpression('user_status')->set($m->refSql('user_id')->fieldQuery('status'));
		$m->addCondition('data_consumed','>=',$m->getElement('net_data_limit'));
		$m->addCondition('net_data_limit','>',0);
		$m->addCondition('reset_date',$date);
		$m->addCondition([['is_expired',false],['is_expired',null]]);

		$grid = $this->add('xepan\hr\Grid');
		$grid->setModel($m,['user','plan','remark','reset_date','net_data_limit','data_consumed','user_status']);
		$grid->addPaginator($ipp=50);

	}
}