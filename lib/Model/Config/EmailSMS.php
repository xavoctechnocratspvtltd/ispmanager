<?php

namespace xavoc\ispmanager;

class Model_Config_EmailSMS extends \xepan\base\Model_ConfigJsonModel{
	public  $fields	= [
						'forgot_password_sms_content'=>"Text",

						'lead_assign_sms_content'=>'Text',
						'lead_assign_email_subject'=>'Line',
						'lead_assign_email_content'=>'xepan\base\RichText',

						'installation_lead_assign_sms_content'=>'Text',
						'installation_lead_assign_email_subject'=>'Line',
						'installation_lead_assign_email_content'=>'xepan\base\RichText',

						'new_account_sms_content'=>'Text',
						'new_account_email_subject'=>'Line',
						'new_account_email_content'=>'xepan\base\RichText',

						'invoice_paid_sms_content'=>'Text',
						'invoice_paid_email_subject'=>'Line',
						'invoice_paid_email_content'=>'xepan\base\RichText',

						'renewal_alert_sms_content'=>'Text',
						'renewal_alert_email_subject'=>'Line',
						'renewal_alert_email_content'=>'xepan\base\RichText',
						'renewal_alert_duration'=>'Line',
						// 'renewal_alert_newsletter_id'=>'DropDown',

						'account_reactivation_sms_content'=>'Text',
						'account_reactivation_email_subject'=>'Line',
						'account_reactivation_email_content'=>'xepan\base\RichText',
						
						'account_reactivation_sms_content'=>'Text',
						'account_reactivation_email_subject'=>'Line',
						'account_reactivation_email_content'=>'xepan\base\RichText',

						'plan_changed_sms_content'=>'Text',
						'plan_changed_email_subject'=>'Line',
						'plan_changed_email_content'=>'xepan\base\RichText',

				];

	public $config_key = 'ISPMANAGER_EMAIL_SMS_CONTENT';
	public $application ='ispmanager';

	function init(){
		parent::init();
		
	}
}