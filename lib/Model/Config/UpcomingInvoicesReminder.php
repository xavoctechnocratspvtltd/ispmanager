<?php

namespace xavoc\ispmanager;

class Model_Config_UpcomingInvoicesReminder extends \xepan\base\Model_ConfigJsonModel{
	public $fields = [
						'days_before_reminder'=>'text',
						'days_after_reminder'=>'text',
						'send_on_invoice_status'=>'DropDown',

						'sms_content'=>'Text',
						'sms_send_from'=>'DropDown',

						'email_subject'=>'line',
						'email_body'=>'xepan\base\RichText',
						'email_send_from'=>'DropDown',
						'send_reminder'=>'checkbox'

					];
	public $config_key = 'UpcomingInvoicesReminder';
	public $application = 'ispmanager';

	function init(){
		parent::init();

		$this->getElement('sms_send_from')->setModel('xepan\communication\Model_Communication_SMSSetting');
		$this->getElement('email_send_from')->setModel($this->add('xepan\communication\Model_Communication_EmailSetting')->addCondition('is_active',true));
		
		$this->getElement('days_before_reminder')->hint('comma seperated values ie. 1,2,4,6');
		$this->getElement('days_after_reminder')->hint('comma seperated values ie. 1,2,4,6');
		
		// $this->getElement('send_on_invoice_status')
		// 	->setValueList(['Draft'=>'Draft','Submitted'=>'Submitted','Redesign'=>'Redesign','Due'=>'Due','Paid'=>'Paid','Canceled'=>'Canceled'])
		// 	;

		$this->getElement('send_reminder')->defaultValue(1);

	}
}