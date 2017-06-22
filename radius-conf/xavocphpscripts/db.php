<?php

$host = 'localhost';
$db_username = 'root';
$db_password = 'winserver';
$database = 'ispmanager';

$db = new PDO('mysql:host='.$host.';dbname='.$database, $db_username, $db_password);


$username = str_replace('"', '', $_SERVER['USER_NAME']);

$user_query = "SELECT * from isp_user where radius_username = '$username'";
$stmt = $db->prepare($user_query);
$stmt->execute();
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

$now = date('Y-m-d H:i:s');
$day = strtolower(date("D", strtotime($now)));


// ===== COPY FROM USER MODEL TO BELOW =========

function getApplicableRow($username,$now,$with_data_limit=false,$less_then_this_id=null){
		
		$day = strtolower(date("D", strtotime($now)));
		$date = 'd'.strtolower(date("d", strtotime($now)));
		$current_time = date("H:i:s",strtotime($now));
		$today = date('Y-m-d',strtotime($now));


		// if start_time is not null then is me in between start-end time
		// is me (day) checked
		// is me (date) checked
		// not expired
		// order by topup, id desc
		// limit 1
		$less_then_id_condition = "";
		if($less_then_this_id) $less_then_id_condition = "AND isp_user_plan_and_topup.id < ".$less_then_this_id;

		$query = "
					SELECT 
						*,
						isp_user_plan_and_topup.id id,
						data_limit + carry_data AS net_data_limit,
						user.last_dl_limit last_dl_limit,
						user.last_ul_limit last_ul_limit,
						IFNULL( (select radacct.acctinputoctets from radacct where username = '$username' and acctstoptime is null) , 0 ) SessionInputOctets ,
						IFNULL( (select radacct.acctoutputoctets  from radacct where username = '$username' and acctstoptime is null), 0 ) SessionOutputOctets
					FROM
						isp_user_plan_and_topup
						JOIN
						isp_user user on isp_user_plan_and_topup.user_id=user.customer_id
					WHERE
						(
							(
								(
									CAST('$current_time' AS time) BETWEEN `start_time` AND `end_time` 
									OR 
									(
										NOT CAST('$current_time' AS time) BETWEEN `end_time` AND `start_time` 
										AND `start_time` > `end_time`
									)
								) 
								AND
								(is_expired=0 or is_expired is null)
							)
							OR
							(
								`start_time` is null
							)
							OR (`start_time`='00:00:00' and `end_time`='00:00:00')
						)
						AND
						`$day`=1
						AND
						`$date` = 1
						AND
						(
							'$now' >= start_date
							AND
							'$now' <= end_date
						)
						AND
							(is_expired=0 or is_expired is null)

						AND
						`user_id`= (SELECT customer_id from isp_user where radius_username = '$username')
						".
						($with_data_limit? " AND data_limit is not null AND data_limit >0 ":'')
						.
						$less_then_id_condition.
						"
						order by is_topup desc, isp_user_plan_and_topup.id desc
						limit 1
						"
						;

		// echo "step 3 in applicable row ".$query;
		$x = runQuery($query,true);
		if(!count($x)) $x= null;
		testDebug('Querying for '.($with_data_limit?'Data Limit':'Bw Limit').' Row ',null,$query);
		testDebug('Found '.($with_data_limit?'Data Limit':'Bw Limit').' Row ',isset($x['remark'])?$x['remark']:'-',$x);
		return $x;
	}

	function checkAuthentication($now,$day,$username, $user_data){

		testDebug('User',null,$user_data);

		$bw_applicable_row = getApplicableRow($username,$now);

		if(!$bw_applicable_row) {
			// exit in radius
			return ['access' => 0];
		}

		$data_limit_row = $bw_applicable_row;
		if(!$bw_applicable_row['net_data_limit']) $data_limit_row = getApplicableRow($username, $now,$with_data_limit=true);
		
		$if_fup='fup_';
		if(($data_limit_row['download_data_consumed'] + $data_limit_row['upload_data_consumed'] + $data_limit_row['SessionInputOctets'] + $data_limit_row['SessionOutputOctets']) < $data_limit_row['net_data_limit']){
			$if_fup='';
		}else{
			if($bw_applicable_row['treat_fup_as_dl_for_last_limit_row']){
				$next_data_limit_row = getApplicableRow($username, $now,null,$data_limit_row['id']);
				
				if( ($next_data_limit_row['download_data_consumed'] + $next_data_limit_row['upload_data_consumed']) > $next_data_limit_row['net_data_limit'] ){
					$data_limit_row['download_limit'] = $next_data_limit_row['fup_download_limit'];
					$data_limit_row['upload_limit'] = $next_data_limit_row['fup_upload_limit'];
					$data_limit_row['remark'] = $next_data_limit_row['remark'];

				}else{
					$data_limit_row['download_limit'] = $bw_applicable_row['fup_download_limit'];
					$data_limit_row['upload_limit'] = $bw_applicable_row['fup_upload_limit'];
					$data_limit_row['remark'] = $next_data_limit_row['remark'];
				}
			}
		}

		// Mark datalimitrow as effective
		runQuery("UPDATE isp_user_plan_and_topup set is_effective=0 where user_id= (SELECT customer_id from isp_user where radius_username = '$username')");
		if($data_limit_row['id']){
			runQuery("UPDATE isp_user_plan_and_topup set is_effective=1 where id=".$data_limit_row['id']);
			testDebug('Setting effective row', $data_limit_row['remark'],$data_limit_row);
		}

		$dl_field = $if_fup.'download_limit';
		$ul_field = $if_fup.'upload_limit';

		// echo "Tmp-String-1 := ".$dl_field.",";
		// echo "Tmp-String-2 := ".$ul_field.",";

		// but from which row ??
		// from applicable if values exists
		$dl_limit = $bw_applicable_row[$dl_field];
		$ul_limit = $bw_applicable_row[$ul_field];
		// echo "Tmp-String-3 := ".$dl_limit.",";
		// echo "Tmp-String-4 := ".$ul_limit.",";

		if($dl_limit === null) $dl_limit = $data_limit_row[$dl_field];
		if($ul_limit === null) $ul_limit = $data_limit_row[$ul_field];
		// from data if not 
		// if fup is null or 0 it is a reject authentication command
		// if user dl, ul, accounting not equal to current dl ul then update
		
		$user_update_query = "UPDATE isp_user SET ";
		$speed_value = null;
		if($dl_limit !== $user_data['last_dl_limit'] || $ul_limit !== $user_data['last_ul_limit'] ){
			$speed_value = "last_dl_limit = ".($dl_limit?:'null').",last_ul_limit = ".($ul_limit?:'null');
			$user_update_query .= $speed_value;
		}

		$accounting_value = null;

		if($user_data['last_accounting_dl_ratio'] != $bw_applicable_row['accounting_download_ratio'] || $user_data['last_accounting_ul_ratio'] != $bw_applicable_row['accounting_upload_ratio']){
			$accounting_value = (($speed_value)?", ":" ")."last_accounting_dl_ratio = ".$bw_applicable_row['accounting_download_ratio'].",last_accounting_ul_ratio = ".$bw_applicable_row['accounting_upload_ratio'];
			$user_update_query .= $accounting_value;
		}
		$user_update_query .= " WHERE radius_username = '$username';";


		$coa = false;
		if($speed_value OR $accounting_value ){
			$coa = true;
			testDebug('Updating User',null,$user_update_query);
			runQuery($user_update_query);
		}

		$final_row = $bw_applicable_row;
		$final_row['dl_limit'] = $dl_limit;
		$final_row['ul_limit'] = $ul_limit;
		$final_row['data_limit'] = $data_limit_row['data_limit'];
		$final_row['carry_data'] = $data_limit_row['carry_data'];
		$final_row['net_data_limit'] = $data_limit_row['net_data_limit'];
		$final_row['download_data_consumed'] = $data_limit_row['download_data_consumed'];
		$final_row['upload_data_consumed'] = $data_limit_row['upload_data_consumed'];
		$final_row['data_limit_row'] = $data_limit_row['remark'];
		$final_row['bw_limit_row'] = $bw_applicable_row['remark'];
		$final_row['coa'] = $coa?'1':'0';
		$final_row['access'] = 1;
		
		if($dl_limit ===null && $ul_limit === null){
			// exit(1);
			$final_row['access'] = 0;
		} 

		return $final_row;

	}

	function updateAccountingData($dl_data,$ul_data,$now,$day,$username, $user_data){

		testDebug('User','in accounting',$user_data);
		
		$consumed_dl_data = ($dl_data*$user_data['last_accounting_dl_ratio']) /100;
		$consumed_ul_data = ($ul_data*$user_data['last_accounting_ul_ratio'])/100;
		// update data query
		$update_query = "
			UPDATE 
				isp_user_plan_and_topup 
			SET 
				download_data_consumed = IFNULL(download_data_consumed,0) + ". ($consumed_dl_data) . ",
				upload_data_consumed = IFNULL(upload_data_consumed,0) + ".($consumed_ul_data) . "
			WHERE 
					is_effective = 1 AND user_id = (SELECT customer_id from isp_user where radius_username = '$username')
				";
		runQuery($update_query);
		testDebug('Updating Accounting Data',['dl'=>byte2human($consumed_dl_data), 'ul'=>byte2human($consumed_ul_data)],$update_query);
	}

	function checkCOA($now,$day,$username,$user_data) {
		$final_row = checkAuthentication($now,$day, $username, $user_data);

		if($final_row['access']==='1' || $final_row['access']===1){
			if($final_row['coa'] === '1' || $final_row['coa'] === 1)
				$final_row['Tmp-Integer-0'] = 1;
			else
				$final_row['Tmp-Integer-0'] = 0;		
		}else{
			$final_row['Tmp-Integer-0'] = 2;
		}

		if($final_row['Tmp-Integer-0']===1){
			$final_row['Tmp-String-0'] = $final_row['ul_limit'].'/'.$final_row['dl_limit'];
		}

		return $final_row;
	}

// ========= COPY FROM USER MODEL TO ABOVE ========

function runQuery($query, $gethash=false){
		global $db;
		if($gethash){
			$stmt = $db->prepare($query);
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_ASSOC);
		}else{
			return $db->exec($query);
		}
}

function testDebug($title,$msg, $details=null){

}

function byte2human($bytes, $decimals = 2){
            $size = array('B','KB','MB','GB','TB','PB','EB','ZB','YB');
            $factor = floor((strlen($bytes) - 1) / 3);
            return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}

function human2byte($value){
              return preg_replace_callback('/^\s*(\d*\.?\d+)\s*(?:([kmgtpy]?)b?)?\s*$/i', function ($m) {
                switch (strtolower($m[2])) {
                  case 'y': $m[1] *= 1024;
                  case 'p': $m[1] *= 1024;
                  case 't': $m[1] *= 1024;
                  case 'g': $m[1] *= 1024;
                  case 'm': $m[1] *= 1024;
                  case 'k': $m[1] *= 1024;
                }
                return $m[1];
              }, $value);
}