<?php

namespace xavoc\ispmanager;

class Model_Plan extends \xepan\commerce\Model_Item{
	// public $table = "isp_plan";
	public $status = ['Published','UnPublished'];
	public $actions = [
				'Published'=>['view','edit','delete','condition'],
				'UnPublished'=>['view','edit','delete','Published']
				];
	
	public $acl_type="ispmanager_plan";

	function init(){
		parent::init();

		// destroy extra fields
		$item_fields = $this->add('xepan\commerce\Model_Item')->getActualFields();
		$required_field = ['name','sku','description','sale_price','original_price','status','document_id','id','created_by','updated_by','created_at','updated_at','type','qty_unit_id','qty_unit','renewable_unit','renewable_value'];
		$destroy_field = array_diff($item_fields, $required_field);
		foreach ($destroy_field as $key => $field) {
			if($this->hasElement($field))
				$this->getElement($field)->destroy();
		}
		$this->getElement('status')->defaultValue('Published');
		
		// if($this->hasElement('minimum_order_qty'))
		// 	$this->getElement('minimum_order_qty')->set(1);

		$plan_j = $this->join('isp_plan.item_id');
		$plan_j->hasOne('xepan\commerce\Model_Taxation','tax_id');

		$plan_j->addField('maintain_data_limit')->type('boolean')->defaultValue(true);

		$plan_j->addField('is_topup')->type('boolean')->defaultValue(false);
		$plan_j->addField('is_auto_renew')->type('boolean')->defaultValue(0);
		$plan_j->addField('available_in_user_control_panel')->type('boolean');
		$plan_j->addField('plan_validity_value')->type('number')->defaultValue(1);

		$this->hasMany('xavoc\ispmanager\Condition','plan_id',null,'conditions');

		$this->addHook('beforeSave',$this,[],4);

		// $this->add('dynamic_model/Controller_AutoCreator');
	}

	function beforeSave(){
		// $this['original_price'] = $this['sale_price'];
		$this['minimum_order_qty'] = 1;
	}

	function page_condition($page){
		$condition_model = $this->add('xavoc\ispmanager\Model_Condition');
		$condition_model->addcondition('plan_id',$this->id);

		$crud = $page->add('xepan\hr\CRUD');

		if($crud->isEditing()){
			$form = $crud->form;
			$form->setLayout('form/condition');
		}

		$crud->setModel($condition_model);

		$crud->grid->removeColumn('plan');
	}

	function import($data){
		// get list of plan
		$plan_list = [];
		foreach ($this->add('xavoc\ispmanager\Model_Plan')->getRows() as $key => $plan) {
			$plan_list[$plan['name']] = $plan['id'];
		}

		// get list of unit
		$unit_list = [];
		foreach ($this->add('xepan\commerce\Model_Unit')->getRows() as $key => $unit) {
			$unit_list[$unit['name']] = $unit['id'];
		}

		// get list of tax
		$tax_list = [];
		foreach ($this->add('xepan\commerce\Model_Taxation')->getRows() as $key => $tax) {
			$tax_list[$tax['name']] = $tax['id'];
		}

		$reset_mode = ['hours'=>'hours','hour'=>'hours','days'=>'days','day'=>'days','months'=>'months','month'=>'months','years'=>'years','year'=>'years'];
		
		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";

		try{

			foreach ($data as $key => $record) {
				// update plan
				$plan_field = ['NAME','CODE','STATUS','ORIGINAL_PRICE','SALE_PRICE','TAX','PLAN_VALIDITY_VALUE','PLAN_VALIDITY_UNIT','DESCRIPTION','RENEWABLE_VALUE','RENEWABLE_UNIT','IS_AUTO_RENEW','AVAILABLE_IN_USER_CONTROL_PANEL'];
				$plan_name = trim($record['NAME']);

				if(!isset($plan_list[$plan_name])){
					$plan_model = $this->add('xavoc\ispmanager\Model_Plan');
					foreach ($plan_field as $key=>$field) {
						$field_name = strtolower(trim($field));
						if($field_name == "code") $field_name = "sku";
						$plan_model[$field_name] = $record[$field];
					}
					$plan_model->save();
					$plan_list[$plan_name] = $plan_model->id;
				}

				$plan_id = $plan_list[$plan_name];


				// unset plan field
				foreach ($plan_field as $key => $field) {
					unset($record[$field]);
				}
				
				//  add condition
				$condition_data = $record;
				$condition = $this->add('xavoc\ispmanager\Model_Condition');
				$condition->addCondition('plan_id',$plan_id);

				foreach ($condition_data as $field => $value) {
					$field = strtolower(trim($field));
					$condition[$field] = $value;
				}
				$condition->save();
			}
		}catch(\Exception $e){

		}

	}

}