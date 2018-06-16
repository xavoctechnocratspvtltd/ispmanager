<?php

namespace xavoc\ispmanager;

class Controller_ResetUserPlanAndTopup extends \AbstractController {

	function run($date = null,$test_user=null,$debug=false){

		if(!$date) $date = $this->app->today;
		// if($testDebug_object) $this->testDebug_object = $testDebug_object;

		// $this->app->db->dsql()->expr('UPDATE isp_user_plan_and_topup SET is_expired =1 WHERE expire_date < "'.$date.'" ')->execute();

		$upt_model = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
		$upt_model->addExpression('reset_date_only')->set('date(reset_date)');

		$upt_model->addCondition([['is_expired',false],['is_expired',null]]);
		$upt_model->addCondition('reset_date_only',$date);

		if($test_user) $upt_model->addCondition('user_id',$test_user->id);

		foreach ($upt_model as $key => $model) {
			try{

				if($debug){
					$mid = $model->id;
					$echo_html = " <br/> ------------ Data Reset User: ".$model['user'];
					$echo_html .= "<table border='1' align='center'>";
					$echo_html .= "<tr>
									<th></th>
									<th>Down Data</th>
									<th>Upload Data</th>
									<th>Session Down Data</th>
									<th>Session Upload Data</th>
									<th>Reset Session Down Data</th>
									<th>Reset Session Upload Data</th>
								</tr>";

					$echo_html .= "<tr><td>Old</td>
									<td>".$model['download_data_consumed']."</td>".
									"<td>".$model['upload_data_consumed']."</td>".
									"<td>".$model['session_download_data_consumed']."</td>".
									"<td>".$model['session_upload_data_consumed']."</td>".
									"<td>".$model['session_download_data_consumed_on_reset']."</td>".
									"<td>".$model['session_upload_data_consumed_on_reset']."</td>".
								"</tr>";
				}

				

				// IF TODAY IS RESET DATE
				// if($model['reset_date'] && strtotime($model['reset_date']) == strtotime($date)){
					//IF DATA IS CARRY FORWARD THEN UPDATE THE DATA LIMIT = (PLAN DATA LIMIT + REMAINING DATA LIMIT OF LAST PERIOD)
				if($model['is_data_carry_forward'] == 'once'){
					$model['carry_data'] = ($model['data_limit'] - $model['data_consumed'])>0?$model['data_limit'] - $model['data_consumed']:0;
				}elseif($model['is_data_carry_forward'] == "allways"){
					$model['carry_data'] = ($model['net_data_limit'] - $model['data_consumed'])>0?($model['net_data_limit'] - $model['data_consumed']):0;
				}

				// RESET TO ZERO OF download_data_consumed AND UPLOAD_data_consumed
				// temporary commented
				$model['download_data_consumed'] = 0;
				$model['upload_data_consumed'] = 0;
				$model['session_download_data_consumed_on_reset'] = $this->app->human2byte($model['session_download_data_consumed']);
				$model['session_upload_data_consumed_on_reset'] = $this->app->human2byte($model['session_upload_data_consumed']);
								
				if($model['data_reset_value']){
					// $model['start_date'] = date("Y-m-d H:i:s", strtotime("+".$model['data_reset_value']." ".$model['data_reset_mode'],strtotime($model['start_date'])));
					// $model['end_date'] = date("Y-m-d H:i:s", strtotime("+".$model['data_reset_value']." ".$model['data_reset_mode'],strtotime($model['end_date'])));
					
					// UPDATE THE RESET DATE = (PLAN RESET INTERVAL + CONDITION RESET DATE)
					if(!$model['is_topup']){
						$reset_date = date("Y-m-d H:i:s", strtotime("+".$model['data_reset_value']." ".$model['data_reset_mode'],strtotime($model['reset_date'])));
						$model['reset_date'] = $reset_date;
						// $this->testDebug('Next reset date set ',$reset_date);
					}else{
						// $this->testDebug('Next reset not set as it is topup ',$model['remark']);
					}
				}
					
				// note: do loop only for reset conditon so move inside
				// is expire
				if(strtotime($model['expire_date']) <= strtotime($date)){
					// $this->testDebug('Marked Expired',null,['Expire Date as per Model' => $model['expire_date']]);				
					$model['is_expired'] = true;
					// $model->saveAndUnload();
				}

				$model->saveAndUnload();

				if($debug){
					$rmodel = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup')->load($mid);
					$echo_html .= "<tr>
									<td>NeW</td>
									<td>".$rmodel['download_data_consumed']."</td>".
									"<td>".$rmodel['upload_data_consumed']."</td>".
									"<td>".$rmodel['session_download_data_consumed']."</td>".
									"<td>".$rmodel['session_upload_data_consumed']."</td>".
									"<td>".$rmodel['session_download_data_consumed_on_reset']."</td>".
									"<td>".$rmodel['session_upload_data_consumed_on_reset']."</td>".
									"</tr>";
					$echo_html .= "</table>";
					
					echo $echo_html;
				}
			// }
				
			// if(strtotime($model['expire_date']) <= strtotime($date)){
			// 		$this->testDebug('Marked Expired',null,['Expire Date as per Model' => $model['expire_date']]);				
			// 		$model['is_expired'] = true;
			// 		$model->saveAndUnload();
			// 	}					
			}catch(\Exception $e){
				
				$model->data['error_log'] = $e->getMessage();

				$log_m = $this->add('xepan\base\Model_AuditLog');
				$log_m['model_class'] = "xavoc\ispmanager\Model_UserPlanAndTopup";
				$log_m['pk_id'] = $model->id;
				$log_m['name'] = json_encode($model->data);
				$log_m['type'] = "Auto Reset Data";
				$log_m->save();
			}
		}
		
		// IF TODAY IS EXPIRE DATE
			//EXPIRE CONDITION
			//SEND EMAIL OR NOTIFY TO CUSTOMER AND ADMIN
		// IF TODAY IS END DATE
			//SEND EMAIL OR NOTIFY TO CUSTOMER AND ADMIN
		
	}
}