<?php

namespace xavoc\ispmanager;

class Model_Channel_Plan extends \xavoc\ispmanager\Model_BasicPlan{
	
	public $status = ['Published','UnPublished'];
	public $actions = [
				'Published'=>['view','edit','delete','condition'],
				'UnPublished'=>['view','edit','delete','condition','publish']
			];

	function init(){
		parent::init();
		
		$join = $this->join('isp_channel_association.plan_id');
		$join->addField('channel_id');
		
		$this->getElement('status')->defaultValue('UnPublished');

		$this->addExpression('channel_name',function($m,$q){
			$asso = $m->add('xavoc\ispmanager\Model_Channel_Association',['channel_title_field'=>'name']);
			$asso->addCondition('plan_id',$m->getElement('id'));
			return $q->expr('[0]',[$asso->fieldQuery('channel')]);
		});
	}
}