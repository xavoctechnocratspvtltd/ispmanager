<?php

namespace xavoc\ispmanager;

class Controller_Reminder extends \AbstractController {
	public $testDebug_object=null;

	function run($date = null,$test_user=null, $testDebug_object=null){

		$config = $this->add('xavoc\ispmanager\Model_Config_EmailSMS');
		$config->tryLoadAny();

		$duration = explode(",",$config['renewal_alert_duration']);
		$isp_user_model = $this->add('xavoc\ispmanager\Model_User');

		if(!$date){
			$date = $this->app->today;
		} 

		foreach ($duration as $day) {
			if(in_array(trim($day),[null,""])) continue;

			$from_date = $to_date = date("Y-m-d", strtotime("+".$day." Days",strtotime($date)));
			$model = $this->add('xavoc\ispmanager\Model_RecurringInvoiceItem',['from_date'=>$from_date,'to_date'=>$to_date]);
			$greet_customers = [];
				
			foreach ($model as $recurring_item) {
				$cust_id = $recurring_item['customer_id'];
				if(isset($greet_customers[$cust_id])) continue;

				try{
					$isp_user_model->load($cust_id);
					if($isp_user_model['status'] == "Active"){
						$this->add('xavoc\ispmanager\Controller_Greet')
							->do($isp_user_model,'renewal_alert');
					}
				}catch(\Exception $e){

				}
				$greet_customers[$cust_id] = $isp_user_model->data;
			}
		}
	}

	function testDebug($title, $msg=null,$detail=null){
		if(!$this->testDebug_object) return;
		$this->testDebug_object->testDebug($title,$msg,$detail);
	}
}