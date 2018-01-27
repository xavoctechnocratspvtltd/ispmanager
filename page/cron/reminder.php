<?php

namespace xavoc\ispmanager;

class page_cron_reminder extends \xepan\base\Page{

	function init(){
		parent::init();

		$date = $this->app->today;
		$dates[$date] = $date;

		$content_model = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
					'renewal_alert_sms_content'=>'Text',
					'renewal_alert_email_subject'=>'Line',
					'renewal_alert_email_content'=>'xepan\base\RichText',
					'renewal_alert_duration'=>'Line'
				],
				'config_key'=>'ISPMANAGER_EMAIL_SMS_CONTENT',
				'application'=>'ispmanager'
			]);
		$content_model->tryLoadAny();

		if($content_model['renewal_alert_duration']){
			$temp = explode(",", $content_model['renewal_alert_duration']);
			foreach ($temp as $key => $days) {
				$t = date("Y-m-d", strtotime('+ '.$days.' Days', strtotime($date)));
				$dates[$t] = $t;
			}
		}

		$model = $this->add('xavoc\ispmanager\Model_RecurringInvoiceItem');
		$model->setOrder('invoice_recurring_date','desc');
		$model->addCondition('invoice_recurring_date',$dates);
		$data = $model->getRows();
		$leads = [];

		foreach ($data as $key => $value){
			$leads[$value['customer_id']] = $value['customer_id'];
		}
		

	}
}