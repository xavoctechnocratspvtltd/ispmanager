<?php

include "db.php";

$bw_applicable_row = getApplicableRow();

if(!count($bw_applicable_row['id'])) exit(1);

// foreach ($_SERVER as $key => $value) {
// 	echo $key . ":=" . $value . "\n";
// }

if($_SERVER['ACCT_STATUS_TYPE'] === 'Start') {
	echo "Tmp-Integer-0 := 1\n";
}else{

	$update_query = "
			UPDATE 
				isp_user_plan_and_topup 
			SET 
				download_data_consumed = IFNULL(download_data_consumed,0) + ". ($_SERVER['ACCT_INPUT_OCTETS']*$bw_applicable_row['accounting_download_ratio']/100) . ",
				upload_data_consumed = IFNULL(upload_data_consumed,0) + ".($_SERVER['ACCT_OUTPUT_OCTETS']*$bw_applicable_row['accounting_upload_ratio']/100) . "
			WHERE 
					is_effective = 1 AND user_id = (SELECT customer_id from isp_user where radius_username = '$username')
				";

	// echo $update_query;

	$db->exec($update_query);

	$bw_applicable_row['download_data_consumed'] += ($_SERVER['ACCT_INPUT_OCTETS']*$bw_applicable_row['accounting_download_ratio']/100);
	$bw_applicable_row['upload_data_consumed'] += ($_SERVER['ACCT_OUTPUT_OCTETS']*$bw_applicable_row['accounting_upload_ratio']/100);


	$data_limit_row = $bw_applicable_row;

	if(!$bw_applicable_row['net_data_limit']) $data_limit_row = getApplicableRow(null,$with_data_limit=true);

	$db->exec("UPDATE isp_user_plan_and_topup set is_effective=0 where user_id= (SELECT customer_id from isp_user where radius_username = '$username')");
	$db->exec("UPDATE isp_user_plan_and_topup set is_effective=1 where id=".$data_limit_row['id']);


	$if_fup='fup_';
	if(($data_limit_row['download_data_consumed'] + $data_limit_row['upload_data_consumed']) < $data_limit_row['net_data_limit']){
		$if_fup='';
	}else{

		if($bw_applicable_row['treat_fup_as_dl_for_last_limit_row']){
			$next_data_limit_row = $this->getApplicableRow(null,null,$data_limit_row['id']);
			
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

	$dl_field = $if_fup.'download_limit';
	$ul_field = $if_fup.'upload_limit';

	// but from which row ??
	// from applicable if values exists
	$dl_limit = $bw_applicable_row[$dl_field];
	$ul_limit = $bw_applicable_row[$ul_field];

	if($dl_limit !== '') $dl_limit = $data_limit_row[$dl_field];
	if($ul_limit !== '') $ul_limit = $data_limit_row[$ul_field];
	// from data if not 
	// if fup is null or 0 it is a reject authentication command 

	// save last dl, ul, Acc_ul, Acc_dl values into user table
	$accounting_data  = ($_SERVER['ACCT_INPUT_OCTETS'] + $_SERVER['ACCT_OUTPUT_OCTETS']);
	$user_query = "SELECT * from isp_user where radius_username = '$username'";
	$stmt = $db->prepare($user_query);
	$stmt->execute();
	$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

	$user_update_query = "UPDATE isp_user SET ";
	$speed_value = "";
	if(!$accounting_data OR ($accounting_data !== null && ($dl_limit !== $user_data['last_dl_limit'] || $ul_limit !== $user_data['last_ul_limit'] || !$access)) ){
		$speed_value = "last_dl_limit = ".$dl_limit.",last_ul_limit = ".$ul_limit;
		$user_update_query .= $speed_value;
	}

	$accounting_value = "";

	if($user_data['last_accounting_dl_ratio'] != $bw_applicable_row['accounting_download_ratio'] || $user_data['last_accounting_ul_ratio'] != $bw_applicable_row['accounting_upload_ratio']){
		$accounting_value = "last_accounting_dl_ratio = ".$bw_applicable_row['accounting_download_ratio'].",last_accounting_ul_ratio = ".$bw_applicable_row['accounting_upload_ratio'];
		$user_update_query .= $accounting_value;
	}

	$user_update_query .= "WHERE user_id = (SELECT customer_id from isp_user where radius_username = '$username');";

	if(count($speed_value) OR count($accounting_value)){
		$db->exec($user_update_query);
	}

	$access= true;
	if(!$dl_limit && !$ul_limit){
		$access = false;
		echo "Tmp-Integer-0 := 0\n";
	}else{
		echo "Tmp-Integer-0 := 1\n";
	}

	echo "Tmp-String-1 := \"$ul_limit/$dl_limit\"\n";
}
// echo "Tmp-String-1 := \"512k/512k\"\n";

// here comes some diffrence from auth.php
// set some control vars so policy xavocacct can send coa based on these vars

// if(!$access) echo "Access-Accept := Reject\n";
// if($dl_limit) echo "Mikrotik-Rate-Limit := \"$ul_limit/$dl_limit\"\n";
