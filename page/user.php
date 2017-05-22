<?php

namespace xavoc\ispmanager;

class page_user extends \xepan\base\Page {
	
	public $title ="User";

	function init(){
		parent::init();

		$model = $this->add('xavoc\ispmanager\Model_User');
		$crud = $this->add('xepan\hr\CRUD');
		if($crud->isEditing()){
			$form = $crud->form;
			$form->setLayout('form/user');
		}
		$crud->setModel($model,['net_data_limit','radius_username','radius_password','plan_id','simultaneous_use','grace_period_in_days','custom_radius_attributes','first_name','last_name','create_invoice','is_invoice_date_first_to_first','include_pro_data_basis','country_id','state_id','city','address','pin_code','qty_unit_id'],['radius_username','plan','simultaneous_use','grace_period_in_days','custom_radius_attributes','first_name','last_name','net_data_limit','is_invoice_date_first_to_first']);
		$crud->grid->removeColumn('attachment_icon');

		if($crud->isEditing()){
			$form = $crud->form;
			$date_to_date_field = $form->getElement('is_invoice_date_first_to_first');
			$date_to_date_field->js(true)->univ()->bindConditionalShow([
				'1'=>['include_pro_data_basis']
			],'div.atk-form-row');
		}

	}
}