<?php

namespace xavoc\ispmanager;

class Model_Channel_Plan extends \xavoc\ispmanager\Model_BasicPlan{
	
	public $status = ['Published','UnPublished'];
	public $actions = [
				'Published'=>['view','edit','delete','condition'],
				'UnPublished'=>['view','edit','delete','condition']
			];

	function init(){
		parent::init();
		
		$join = $this->join('isp_channel_association.plan_id');
		$join->addField('channel_id');
		
		$this->getElement('status')
			->defaultValue('UnPublished');
	}
}