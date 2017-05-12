<?php


namespace xavoc\ispmanager;


class Controller_ResetUserPlanAndTopup extends \AbstractController {
	public $testDebug_object=null;

	function run($date = null,$test_user=null, $testDebug_object=null){
		if(!$date) $date = $this->app->now;
		if($testDebug_object) $this->testDebug_object = $testDebug_object;

		// $this->app->db->dsql()->expr('UPDATE isp_user_plan_and_topup SET is_expired =1 WHERE expire_date < "'.$date.'" ')->execute();

		$upt_model = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
		$upt_model->addCondition([['is_expired',false],['is_expired',null]]);
		if($test_user) $upt_model->addCondition('user_id',$test_user->id);


		foreach ($upt_model as $key => $model) {
			$this->testDebug('Checking non expired',$model['remark'],['existing_reset_date'=>$model['reset_date'],'on_date'=>$date]);
			// IF TODAY IS RESET DATE
			if($model['reset_date'] && strtotime($model['reset_date']) <= strtotime($date)){
				//IF DATA IS CARRY FORWARD THEN UPDATE THE DATA LIMIT = (PLAN DATA LIMIT + REMAINING DATA LIMIT OF LAST PERIOD)
				if($model['is_data_carry_forward'] == 'once'){
					$model['carry_data'] = ($model['data_limit'] - $model['data_consumed'])>0?$model['data_limit'] - $model['data_consumed']:0;
				}elseif($model['is_data_carry_forward'] == "allways"){
					$model['carry_data'] = ($model['net_data_limit'] - $model['data_consumed'])>0?($model['net_data_limit'] - $model['data_consumed']):0;
				}

				$this->testDebug('Data Reset Required',null, ['Reset Date ' => $model['reset_date'] , 'Data Carry Mode'=>$model['is_data_carry_forward'], 'carry_data'=> $model['carry_data']]);
			
				// RESET TO ZERO OF download_data_consumed AND UPLOAD_data_consumed
				$model['download_data_consumed'] = 0;
				$model['upload_data_consumed'] = 0;

				if($model['data_reset_value']){
					$model['start_date'] = date("Y-m-d H:i:s", strtotime("+".$model['data_reset_value']." ".$model['data_reset_mode'],strtotime($model['start_date'])));;
					$model['end_date'] = date("Y-m-d H:i:s", strtotime("+".$model['data_reset_value']." ".$model['data_reset_mode'],strtotime($model['end_date'])));;
					// UPDATE THE RESET DATE = (PLAN RESET INTERVAL + CONDITION RESET DATE)
					$reset_date = date("Y-m-d H:i:s", strtotime("+".$model['data_reset_value']." ".$model['data_reset_mode'],strtotime($model['reset_date'])));
					$model['reset_date'] = $reset_date;
					$this->testDebug('Next reset date set ',$reset_date);
				}
			}
			
			// is expire
			if(strtotime($model['expire_date']) <= strtotime($date)){
				$this->testDebug('Marked Expired',null,['Expire Date as per Model' => $model['expire_date']]);
				$model['is_expired'] = true;
			}
			$model->saveAndUnload();
		}
		
		// IF TODAY IS EXPIRE DATE
			//EXPIRE CONDITION
			//SEND EMAIL OR NOTIFY TO CUSTOMER AND ADMIN
		// IF TODAY IS END DATE
			//SEND EMAIL OR NOTIFY TO CUSTOMER AND ADMIN
		
	}

	function testDebug($title, $msg=null,$detail=null){
		if(!$this->testDebug_object) return;
		$this->testDebug_object->testDebug($title,$msg,$detail);
	}
}