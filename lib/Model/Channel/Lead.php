<?php

namespace xavoc\ispmanager;

class Model_Channel_Lead extends \xavoc\ispmanager\Model_Lead{

	public $status = ['Active','InActive'];
	public $actions = [
					'Active'=>['view','open','deactivate','communication','edit','delete'],
					'Open'=>['view','assign','close','lost','communication','edit','delete'],
					'Won'=>['view','edit','delete','communication'],
					'Lost'=>['view','open','communication','edit','delete'],
					'InActive'=>['view','edit','delete','activate','communication']
				];

	function init(){
		parent::init();
		
		$join = $this->join('isp_channel_association.lead_id');
		$join->addField('channel_id');
		
		$this->getElement('status')->defaultValue('Open');
	}

	// function page_plan_activate($page){
	// 	$cust = $this->add('xepan\commerce\Model_Customer');
	// 	$cust->addCondition('id',$this->id);
	// 	$cust->tryLoadAny();
	// 	if(!$cust->loaded()){
	// 		$page->add('View')->set('isp user account not found')->addClass('alert alert-danger');
	// 	}
		
	// 	$isp_user = $this->add('xavoc\ispmanager\Model_User');
	// 	$isp_user->addCondition('customer_id',$cust->id);
	// 	$isp_user->tryLoadAny();
	// 	if(!$isp_user->loaded()){
	// 		$page->add('View')->set('isp user account not found')->addClass('alert alert-danger');
	// 	}

	// 	$isp_user->page_active($page);
	// }
	// function paln_activate($isp_user_model){
	// 	if(!$isp_user_model->loaded()) throw new \Exception("isp model must loaded");
		
	// 	$isp_user_model->active();
	// }
}