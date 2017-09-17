<?php

namespace xavoc\ispmanager;

/**
* 
*/
class Tool_CustomerRegistration extends \xepan\cms\View_Tool{
	public $options=[];
	function init(){
		parent::init();

		if($this->owner instanceof \AbstractController) return;
		
		if(!$this->app->auth->isLoggedIn()){
			$this->app->redirect($this->app->url('staff_login'));
			return;
		}

		$user_model = $this->add('xavoc\ispmanager\Model_User');

		$form = $this->add('Form');
		$form->setLayout(['form/staff/userregistration']);
		$form->setModel($user_model,['plan_id','plan','radius_username','radius_password','simultaneous_use','grace_period_in_days','custom_radius_attributes','mac_address','first_name','last_name','country_id','state_id','city','address','pin_code','organization','billing_address','billing_city','billing_pincode','create_invoice']);
		$form->addField('DatePicker','dob');
		$form->addField('pan_number');
		$form->addField('xepan\base\DropDownNormal','status_of_organization')->setValueList(['Partership_Firm'=>'Partership Firm','Public_Limited_Co.'=>'Public Limited Co','Education_institute'=>'Education institute','Private_limited_company'=>'Private Limited Company','Trust'=>'Trust','Goverment_Organization'=>'Goverment Organization','Other'=>'Other,Please Specify']);
		$form->addField('xepan\base\DropDownNormal','customer_cat')->setValueList(['Name_Account'=>'Name Account','sme_smb'=>'SME / SMB','SOHO'=>'SOHO','Residential'=>'Residential','ISP'=>'ISP (Please attach a copy of ISP liense)','PCOs'=>'PCOs','OSP_BPO'=>'OSP / BPO (Please attach a copy of ISP liense given by DOT)']);
		$form->addField('Radio','static_ip')->setValueList(['Yes'=>'Yes','No'=>'No'])->addClass('ispmanager-radio_btn');
		$multi_upload_field = $form->addField('xepan\base\Form_Field_Upload','attachment',"")
									->allowMultiple()->addClass('xepan-padding');
		$filestore_image=$this->add('xepan\filestore\Model_File',['policy_add_new_type'=>true]);
		$multi_upload_field->setModel($filestore_image);	
	
		$form->addSubmit("Submit")->addClass('btn btn-success');
	
		if($form->isSubmitted()){
			$form->update();
			$upload_images_array = explode(",",$form['attachment']);
			foreach ($upload_images_array as $file_id) {
				$user_model->addAttachment($file_id,'attach');
			}

			$this->add('xepan\base\Model_Contact_Event')
					->addCondition('contact_id',$form->model->id)
					->addCondition('head',"DOB")
					->addCondition('value',$form['dob'])
					->tryLoadAny()
					->save();
			if($form['create_invoice']){
				$form->js(null,$form->js()->reload())->redirect($this->app->url('staff_received-payment',['customer_id'=>$form->model->id]))->execute();
			}else{
				$form->js(null,$form->js()->reload())->univ()->successMessage('Customer Created')->execute();
			}	
		}
	}
}