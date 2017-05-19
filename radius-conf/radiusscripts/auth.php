<?php

include "db.php";

$final_row = checkAuthentication($now,$day,$username, $user_data);
if(!$final_row['access']) exit(1);
if($final_row['dl_limit']){
	$ul_limit = $final_row['ul_limit'];
	$dl_limit = $final_row['dl_limit'];
	echo "Mikrotik-Rate-Limit := \"$ul_limit/$dl_limit\"\n";
}
