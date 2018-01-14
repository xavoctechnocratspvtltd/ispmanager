<?php

namespace xavoc\ispmanager;

class Form_CAF extends \Form{
	public $model;
	public $mandatory_field = [];  // field_name => validation rule

	function init(){
		parent::init();

		if(!$this->model)
			$this->model = $model = $this->add('xavoc\ispmanager\Model_User');

		$config = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'attachment_type'=>'text'
						],
					'config_key'=>'ISPMANAGER_MISC',
					'application'=>'ispmanager'
			]);
		$config->add('xepan\hr\Controller_ACL');
		$config->tryLoadAny();
		$this->attachment_type = $attachment_type = [];
		$this->attachment_fields = $attachment_fields = [];
		if($config['attachment_type']){
			$attachment_type = explode(",", $config['attachment_type']);
			foreach ($attachment_type as $key => $value) {
				$attachment_fields[$this->app->normalizeName($value)] = "c1~12";
			}
		}

		$model_layout_fields = [
					'first_name'=>'Basic Detail~c1~3',
					'last_name'=>'c2~3',
					'organization'=>'c3~3',
					'customer_type'=>'c4~3',
					'image_id~Photo'=>'c5~3',
					'tin_no'=>'c6~3',
					'pan_no'=>'c7~3',
					'gstin'=>'c8~3',
					'website'=>'c9~3',

					'shipping_country_id~Country'=>'Service Installation Address~b1~6',
					'shipping_state_id~State'=>'b1~6',
					'shipping_city~City'=>'b1~6',
					'shipping_pincode~Pincode'=>'b1~6',
					'shipping_address~Address'=>'b2~6',
					'same_as_billing_address~Billing Address Same as Installation Address'=>'b2~6',

					'billing_country_id'=>'Billing Address~b1~6~closed',
					'billing_state_id'=>'b1~6',
					'billing_city'=>'b1~6',
					'billing_pincode'=>'b1~6',
					'billing_address'=>'b2~6',

					'plan'=>'Plan/ Radius Information~c1~4',
					'radius_username'=>'c2~4',
					'radius_password'=>'c3~4',
					'mac_address'=>'c4~6',
					'simultaneous_use'=>'c4~6',
					'grace_period_in_days'=>'c4~6',
					'custom_radius_attributes'=>'c5~6',
					'create_invoice~'=>'c8~4',
					'is_invoice_date_first_to_first~'=>'c9~4',
					'include_pro_data_basis'=>'c10~4',
					'documents'=>'Documents~c1-12',
				];
		$layout_array = array_merge($model_layout_fields,$attachment_fields);

		$this->add('xepan\base\Controller_FLC')
				->addContentSpot()
				->makePanelsCoppalsible()
				->layout($layout_array);

		$this->setModel($this->model,['first_name','last_name','organization','customer_type','image_id','tin_no','pan_no','gstin','website','shipping_country_id','shipping_state_id','shipping_city','shipping_address','shipping_pincode','same_as_billing_address','billing_country_id','billing_state_id','billing_city','billing_pincode','billing_address','plan','plan_id','radius_username','radius_password','mac_address','simultaneous_use','grace_period_in_days','custom_radius_attributes','create_invoice','is_invoice_date_first_to_first','include_pro_data_basis']);

		foreach ($attachment_type as $key => $value) {
			$attachment_name = $this->app->normalizeName($value);

			$field = $this->addField('xepan\base\Upload',$attachment_name,$value);
			$field->setModel('xepan\filestore\Image');

			$attachment = $this->add('xavoc\ispmanager\Model_Attachment');
			$attachment->addCondition('contact_id',$this->model->id);
			$attachment->addCondition('title',$attachment_name);
			$attachment->tryLoadAny();

			$field->set($attachment['file_id']);
			
		}
		// billing address
		$country_field =  $this->getElement('billing_country_id');
		$country_field->getModel()->addCondition('status','Active');
		$state_field = $this->getElement('billing_state_id');
		$state_field->getModel()->addCondition('status','Active');
		if($country_id = $this->app->stickyGET('country_id')){
			$state_field->getModel()->addCondition('country_id',$country_id);
		}
		// $country_field->js('change',$form->js()->atk4_form('reloadField','state_id',[$this->app->url(),'country_id'=>$state_field->js()->val()]));
		$country_field->js('change',$state_field->js()->reload(null,null,[$this->app->url(null,['cut_object'=>$state_field->name]),'country_id'=>$country_field->js()->val()]));

		// shipping address
		$s_country_field =  $this->getElement('shipping_country_id');
		$s_country_field->getModel()->addCondition('status','Active');
		$s_state_field = $this->getElement('shipping_state_id');
		$s_state_field->getModel()->addCondition('status','Active');

		if($s_country_id = $this->app->stickyGET('s_country_id')){
			$s_state_field->getModel()->addCondition('s_country_id',$s_country_id);
		}
		// $country_field->js('change',$form->js()->atk4_form('reloadField','state_id',[$this->app->url(),'country_id'=>$state_field->js()->val()]));
		$s_country_field->js('change',$s_state_field->js()->reload(null,null,[$this->app->url(null,['cut_object'=>$s_state_field->name]),'country_id'=>$s_country_field->js()->val()]));

		// mandatory field 
		foreach ($this->mandatory_field as $field_name => $validation) {
			$this->getElement($field_name)->validate($validation);
		}

		$this->addSubmit('Save')->addClass('btn btn-primary btn-block');
		
	}


	function process(){
		if($this->isSubmitted()){
			$this->hook('CAF_BeforeSave',[$this]);
			$this->save();

			// attachment entry
			if(isset($this->attachment_type)){
				foreach ($this->attachment_type as $key => $value) {
					$attachment_name = $this->app->normalizeName($value);
					if($this[$attachment_name]){
						$attachment = $this->add('xavoc\ispmanager\Model_Attachment');
						$attachment->addCondition('contact_id',$this->model->id);
						$attachment->addCondition('title',$attachment_name);
						$attachment->tryLoadAny();
						
						$attachment['file_id'] = $this[$attachment_name];
						$attachment->save();
					}
				}
			}
			$this->hook('CAF_AfterSave',[$this]);
		}
	}
}