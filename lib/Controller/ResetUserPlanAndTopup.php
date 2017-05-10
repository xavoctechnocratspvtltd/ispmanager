<?php


namespace xavoc\ispmanager;


class Controller_ResetUserPlanAndTopup extends \AbstractController {
	
	function run($date = null){
		if(!$date) $date = $this->app->now;

		$upt_model = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
		$upt_model->addCondition('is_expired',false);


		foreach ($upt_model as $key => $model) {
			// IF TODAY IS RESET DATE
			if($model['reset_date'] <= $date){
				if($model['is_data_carry_forward']){
					//IF DATA IS CARRY FORWARD THEN UPDATE THE DATA LIMIT = (PLAN DATA LIMIT + REMAINING DATA LIMIT OF LAST PERIOD)
				}

				// RESET TO ZERO OF download_data_consumed AND UPLOAD_data_consumed
				$model['download_data_consumed'] = 0;
				$model['upload_data_consumed'] = 0;

				// UPDATE THE RESET DATE = (PLAN RESET INTERVAL + CONDITION RESET DATE)
				$reset_date = date("Y-m-d H:i:s", strtotime("+".$model['data_reset_value']." ".$model['data_reset_mode'],strtotime($model['reset_date'])));
				$model['reset_date'] = $reset_date;

				// is expire
				if($model['expire_date'] <= $date)
					$model['is_expired'] = true;

				$model->saveAndUnload();
			}
			
		}
		
		// IF TODAY IS EXPIRE DATE
			//EXPIRE CONDITION
			//SEND EMAIL OR NOTIFY TO CUSTOMER AND ADMIN
		// IF TODAY IS END DATE
			//SEND EMAIL OR NOTIFY TO CUSTOMER AND ADMIN

		
	}
}