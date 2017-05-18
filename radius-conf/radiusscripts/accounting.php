<?php

include "db.php";

if($_SERVER['ACCT_STATUS_TYPE'] === 'Start') exit(0);

$dl_data = $_SERVER['ACCT_INPUT_OCTETS'];
$ul_data = $_SERVER['ACCT_OUTPUT_OCTETS'];

$final_row = updateAccountingData($dl_data,$ul_data,$now,$day,$username, $user_data);

if($final_row['Tmp-Integer-0']==='1'){
	// coa
	echo "Tmp-Integer-0 := 1";
	echo "Tmp-String-0 := ".$final_row['ul_limit'].'/'.$final_row['dl_limit'];
}elseif($final_row['Tmp-Integer-0']==='2'){
	// disconnect
	echo "Tmp-Integer-0 := 2";
}else{
	// no change
	echo "Tmp-Integer-0 := 0";
}