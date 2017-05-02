<?php

namespace xavoc\ispmanager;

class Model_Plan extends \xepan\base\Model_Table{
	public $table = "isp_plan";
	public $status = ['active','deactive'];
	public $actions = [
				'active'=>['view','edit','delete','policy'],
				'deactive'=>['view','edit','delete','active']
				];
	public $acl_type="ispmanager_plan";
	function init(){
		parent::init();

		$this->addField('name');
		$this->addField('description');
		$this->addField('total_limit')->type('number');
		$this->addField('price')->type('number');
		$this->addField('mode')->enum(['monthly','quarterly','half yearly','yearly']);
		$this->addField('type_of_plan')->enum(['prepaid','postpaid']);
		$this->addField('data_rate_dl')->type('number')->hint('DL: Download Limit');
		$this->addField('data_rate_ul')->type('number')->hint('UL: Upload Limit');
		
		$this->addField('after_limit')->enum(['close','capping']);
		$this->addField('after_limit_dl')->type('number')->hint('DL: Download Limit')->defaultValue(0);
		$this->addField('after_limit_ul')->type('number')->hint('UL: Upload Limit')->defaultValue(0);

		$this->addField('available_in_user_control_panel')->type('boolean');
		$this->addField('status')->enum(['active','deactive'])->defaultValue('active');

		$this->hasMany('xavoc\ispmanager\Policy','plan_id');
		$this->add('dynamic_model/Controller_AutoCreator');
	}

	function page_policy($page){
		$model = $this->add('xavoc\ispmanager\Model_Policy');
		$model->addCondition('plan_id',$this->id);

		$crud = $page->add('xepan\hr\CRUD');
		$crud->setModel($model);
		
		$crud->grid->removeColumn('attachment_icon');
		$crud->grid->removeColumn('action');

		$crud->grid->add('VirtualPage')
			->addColumn('condition')
			->set(function($v_page){
				$id = $_GET[$v_page->short_name.'_id'];
				$condition_model = $this->add('xavoc\ispmanager\Model_Condition');
				$condition_model->addcondition('policy_id',$id);

				$crud = $v_page->add('xepan\hr\CRUD');
				$crud->setModel($condition_model);
		});
	}
}