<?php


namespace xavoc\ispmanager;


class Controller_ResetUserPlanAndTopup extends \AbstractController {
	
	function run($date = null){
		if(!$date) $date = $this->app->now;

		// IF TODAY IS RESET DATE
			//IF DATA IS CARRY FORWARD THEN UPDATE THE DATA LIMIT = (PLAN DATA LIMIT + REMAINING DATA LIMIT OF LAST PERIOD)
			// RESET TO ZERO OF download_data_consumed AND UPLOAD_data_consumed
			// UPDATE THE RESET DATE = (PLAN RESET INTERVAL + CONDITION RESET DATE)
		
		// IF TODAY IS EXPIRE DATE
			//EXPIRE CONDITION
			//SEND EMAIL OR NOTIFY TO CUSTOMER AND ADMIN
		// IF TODAY IS END DATE
			//SEND EMAIL OR NOTIFY TO CUSTOMER AND ADMIN

		
	}
}