<?php

$host = 'localhost';
$db_username = 'root';
$db_password = 'winserver';
$database = 'ispmanager';

$db = new PDO('mysql:host='.$host.';dbname='.$database, $db_username, $db_password);

$username = str_replace('"', '', $_SERVER['USER_NAME']);


function getApplicableRow($now=null,$with_data_limit=false,$less_then_this_id=0){
		global $db;
		global $username;

		if(!$now) $now = date('Y-m-d H:i:s');
		
		$day = strtolower(date("D", strtotime($now)));
		$date = 'd'.strtolower(date("d", strtotime($now)));
		$current_time = date("H:i:s",strtotime($now));
		$today = date('Y-m-d',strtotime($now));
		
		$less_then_id_condition = "";
		if($less_then_this_id) $less_then_id_condition = "AND isp_user_plan_and_topup.id < ".$less_then_this_id;
		// if start_time is not null then is me in between start-end time
		// is me (day) checked
		// is me (date) checked
		// not expired
		// order by topup, id desc
		// limit 1

		$query = "
					SELECT 
						*, 
						data_limit + carry_data AS net_data_limit
					FROM
						isp_user_plan_and_topup
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
						order by is_topup desc, id desc
						limit 1
						"
						;

		$stmt = $db->prepare($query);
		$stmt->execute();
		$r = $stmt->fetch(PDO::FETCH_ASSOC);
		return $r;
		// return $stmt->fetchAll(PDO::FETCH_ASSOC);
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