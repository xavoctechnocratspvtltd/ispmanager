<?php

namespace xavoc\ispmanager;

/**
* 
*/
class Tool_CustomerRegistration extends \xepan\cms\View_Tool{
	public $options=[];
	function init(){
		parent::init();

		$user_model = $this->add('xavoc\ispmanager\Model_User');

		$form = $this->add('Form');
		$form->setLayout(['form/staff/userregistration']);
		$form->setModel($user_model,['plan_id','plan','radius_username','radius_password','simultaneous_use','grace_period_in_days','custom_radius_attributes','mac_address','first_name','last_name','country_id','state_id','city','address','pin_code','organization','billing_address','billing_city','billing_pincode']);
		$form->addField('dob');
		$form->addField('pan_number');
		$form->addField('xepan\base\DropDownNormal','status_of_organization')->setValueList(['Partership_Firm'=>'Partership Firm','Public_Limited_Co.'=>'Public Limited Co','Education_institute'=>'Education institute','Private_limited_company'=>'Private Limited Company','Trust'=>'Trust','Goverment_Organization'=>'Goverment Organization','Other'=>'Other,Please Specify']);
		$form->addField('xepan\base\DropDownNormal','customer_cat')->setValueList(['Name_Account'=>'Name Account','sme_smb'=>'SME / SMB','SOHO'=>'SOHO','Residential'=>'Residential','ISP'=>'ISP (Please attach a copy of ISP liense)','PCOs'=>'PCOs','OSP_BPO'=>'OSP / BPO (Please attach a copy of ISP liense given by DOT)']);
		// $form->addField('general_contact');
		// $form->addField('technical_contact');
		// $form->addField('finance_contact');
		// $form->addField('general_email');
		// $form->addField('technical_email');
		// $form->addField('finance_email');
		// $form->addField('Checkbox','same_as','Same as Installtion Address');
		// $form->addField('Number','no_of_connection');
		// $form->addField('rate_plan_code');
		// $form->addField('internet_speed');
		// $form->addField('Radio','bytes')->setValueList(['KB'=>'Kbps','MB'=>'Mbps']);
		// $form->addField('Radio','type_of_access')->setValueList(['Dedicated'=>'Dedicated','Shared'=>'Shared']);
		// $form->addField('Radio','lastmile_detail')->setValueList(['Provide_by_customer'=>'Provide By Customer','Installtion_Dedicated'=>'Installtion by Prompt Infracom Pvt. Ltd. dedicated to customer & owned by cystomer','Installtion_shared_basic'=>'Installtion by Prompt Infracom Pvt. Ltd. on shared basic & owned by Prompt Infracom Pvt. Ltd.']);
		// $form->addField('make');
		// $form->addField('mode');
		// $form->addField('serial');
		$form->addField('Radio','static_ip')->setValueList(['Yes'=>'Yes','No'=>'No'])->addClass('ispmanager-radio_btn');
		$form->addSubmit("Submit")->addClass('btn btn-success');
		
		if($form->isSubmitted()){
			$form->update();
			$form->js(null,$form->js()->reload())->univ()->successMessage('user created');
		}
	}
}