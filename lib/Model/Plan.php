<?php

namespace xavoc\ispmanager;

class Model_Plan extends \xepan\base\Model_Table{
	public $table = "isp_plan";
	public $status = ['active','deactive'];
	public $actions = [
				'active'=>['view','edit','delete','condition'],
				'deactive'=>['view','edit','delete','active']
				];
	public $acl_type="ispmanager_plan";
	function init(){
		parent::init();

		$this->addField('name');
		$this->addField('description');
		$this->addField('price')->type('number');

		$this->addField('available_in_user_control_panel')->type('boolean');
		$this->addField('status')->enum(['active','deactive'])->defaultValue('active');
		$this->addField('is_topup')->type('boolean')->defaultValue(false);
		$this->addField('maintain_data_limit')->type('boolean')->defaultValue(true);
		
		$this->addField('is_auto_renew')->type('boolean')->defaultValue(0);
		// if condition is recurring then show
		$this->addField('period')->type('number');
		$this->addField('period_unit')->enum(['hours','days','months','years']);

		$this->hasMany('xavoc\ispmanager\Condition','plan_id',null,'conditions');

		$this->add('dynamic_model/Controller_AutoCreator');
	}

	function page_condition($page){
		$condition_model = $this->add('xavoc\ispmanager\Model_Condition');
		$condition_model->addcondition('plan_id',$this->id);

		$crud = $page->add('xepan\hr\CRUD');
		$crud->setModel($condition_model);
		// if($crud->isEditing()){
		// 	$form = $crud->form;
		// 	$recurring_field = $form->getElement('is_recurring');
		// 	$recurring_field->js(true)->univ()
		// 			->bindConditionalShow([
		// 				'1'=>['data_reset_value','data_reset_mode'],
		// 			],'div.atk-form-row');
		// }
	}
}