<?php

include "db.php";

if(in_array($_SERVER['ACCT_STATUS_TYPE'], ['Start','Accounting-On'])){
	echo "Tmp-Integer-0 := 0";
	exit;
}elseif($_SERVER['ACCT_STATUS_TYPE'] == 'Stop'){
	$dl_data = $_SERVER['ACCT_INPUT_OCTETS'];
	$ul_data = $_SERVER['ACCT_OUTPUT_OCTETS'];
	updateAccountingData($dl_data,$ul_data,$now,$day,$username, $user_data);
	echo "Tmp-Integer-0 := 0";
	exit;
}else{
	// it's interim
	$final_row = checkCOA($now,$day,$username, $user_data);
	if($final_row['Tmp-Integer-0']===1){
		// coa
		echo "Tmp-Integer-0 := 1,";
		echo "Tmp-String-0 := ".$final_row['ul_limit'].'/'.$final_row['dl_limit'];
	}elseif($final_row['Tmp-Integer-0']===2){
		// disconnect
		echo "Tmp-Integer-0 := 2";
	}else{
		// no change
		echo "Tmp-Integer-0 := 0";
	}
}
