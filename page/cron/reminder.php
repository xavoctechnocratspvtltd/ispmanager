<?php

namespace xavoc\ispmanager;

class page_cron_reminder extends \xepan\base\Page{

	function init(){
		parent::init();
		
		$debug = $_GET['debug'];
		$date = $_GET['on_date']?:$this->app->today;
		$dates[$date] = $date;
		
		$content_model = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
					'renewal_alert_sms_content'=>'Text',
					'renewal_alert_email_subject'=>'Line',
					'renewal_alert_email_content'=>'xepan\base\RichText',
					'renewal_alert_newsletter_id'=>'DropDown',
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

		$campaign_detail = [
				'title'=>"Reminder Alert",
				'starting_date'=>$this->app->today,
				'ending_date'=>$this->app->nextDate($this->app->today),
				'campaign_type'=>'campaign',
				'status'=>'Draft',
			];

		$new_model = $this->add('xepan\marketing\Model_Newsletter');
		$new_model->load($content_model['renewal_alert_newsletter_id']);

		$document_list = [];
		$document_list[$new_model->id] = ['date'=>$this->app->today];

			$campaign = $this->add('xepan\marketing\Model_Campaign');
			$cmp_model = $campaign->scheduleCampaign($campaign_detail,[$this->app->today],$leads,$document_list,$delete_old_association=true);
			$cmp_model->approve();
		
		if($debug){
			$this->add('View')->setHtml('Dates :'.count($dates).' <br/>'.implode(", ", $dates));
			$this->add('View')->setHtml('Leads :'.count($leads).' <br/>'.implode(", ", $leads));
			$this->add('View')->setHtml('New Letters :<br/>Subject: '.$new_model['title']."<br/> Body: ".$new_model['message_blog']);
			$this->add('View')->set('Campaign Detail');
		}

	}
}