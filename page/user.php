<?php

namespace xavoc\ispmanager;


class page_user extends \xepan\base\Page {
	
	public $title ="User";

	function init(){
		parent::init();

		$model = $this->add('xavoc\ispmanager\Model_User');
		$crud = $this->add('xepan\hr\CRUD');
		$crud->setModel($model,['radius_username','radius_password','plan_id','simultaneous_use','grace_period_in_days','custom_radius_attributes','first_name','last_name','create_invoice','include_pro_data_basis'],['radius_username','plan','simultaneous_use','grace_period_in_days','custom_radius_attributes','first_name','last_name']);
		$crud->grid->removeColumn('attachment_icon');
	}
}