<?php

namespace xavoc\ispmanager;

class page_cron_resetUserPlanAndCondition extends \xepan\base\Page{

	function run($date = null){
		if(!$date) $date = $this->app->now;

		/*
		****
		implementing plans for 1st month it's invoice is paid 
		DL = 50GB start date = "20-may-2017" end-date = "20-june-2017" expire date ="25-june-2017"
		// next month june on date 20-june
			send email or notify to admin about the user plan workingig in grace period please paid your account or activate
			// on date 25-june-2017
			// expire the plan and send email

		actually cron job only set the plan or topis is expired or not
		reseting the values part of at time of invoice PaidAndActivate function
		****
		*/

	}
}