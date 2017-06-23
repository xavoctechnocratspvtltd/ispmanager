<?php

function getAAADetails($now=null,$accounting_data=null,$human_redable=false){
	if(!$now) $now = isset($this->app->ispnow)? $this->app->ispnow : $this->app->now;

	$day = strtolower(date("D", strtotime($now)));

	$this->testDebug("====================",'');
	if(!$accounting_data)
		$this->testDebug('Authentication on ', $now . " [ $day ]");
	else
		$this->testDebug('Accounting on ', $now . " [ $day ]");
	// if accounting data
		// add in effective_row=1
	if($accounting_data){
		if(!is_array($accounting_data)){
			$accounting_data=[$accounting_data,0];
		}

		$condition = "is_effective = 1 AND user_id = ". $this->id;
		$update_query = "UPDATE isp_user_plan_and_topup SET download_data_consumed = IFNULL(download_data_consumed,0) + ".($this->app->human2byte($accounting_data[0])*$this['last_accounting_dl_ratio']/100) . " , upload_data_consumed = IFNULL(upload_data_consumed,0) + ".($this->app->human2byte($accounting_data[1])*$this['last_accounting_ul_ratio']/100) . " WHERE ". $condition;
		$this->app->db->dsql()->expr($update_query)->execute();
		
		$data = $this->app->db->dsql()->table('isp_user_plan_and_topup')->field('download_data_consumed')->field('upload_data_consumed')->field('remark')->where($this->db->dsql()->expr($condition))->getHash();
		$data['download_data_consumed'] = $this->app->byte2human($data['download_data_consumed']);
		$data['upload_data_consumed'] = $this->app->byte2human($data['upload_data_consumed']);

		$accounting_data['remark']= $data['remark'];
		$accounting_data['dl_ratio']= $this['last_accounting_dl_ratio'];
		$accounting_data['ul_ratio']= $this['last_accounting_ul_ratio'];

		$this->testDebug('Saving Accounting Data ',$accounting_data,$update_query);
		$this->testDebug('Total Accounting data ',$data);
	}
	// --------------------- end of accounting

	$bw_applicable_row = $this->getApplicableRow($now);
	$this->testDebug('Applicable Row ', $bw_applicable_row['remark'],$bw_applicable_row);
	// run effectiveDataRecord again to set flag in database
	// run getDlUl
	// echo $bw_applicable_row['net_data_limit']." = ".$bw_applicable_row['download_data_consumed'] ." + ".$bw_applicable_row['upload_data_consumed']."<br/>";
	$data_limit_row = $bw_applicable_row;

	if(!$bw_applicable_row['net_data_limit']) $data_limit_row = $this->getApplicableRow($now,$with_data_limit=true);
	$this->testDebug('Applicable Data Row ', $data_limit_row['remark']);

	// bandwidth or fup ??
	$if_fup='fup_';
	if(($data_limit_row['download_data_consumed'] + $data_limit_row['upload_data_consumed']) < $data_limit_row['net_data_limit']){
		$this->testDebug('Under Data Limit',null,['download_data_consumed'=>$data_limit_row['download_data_consumed'] ,'upload_data_consumed'=> $data_limit_row['upload_data_consumed'],'net_data_limit'=> $data_limit_row['net_data_limit']]);
		$if_fup='';
	}else{
		// this is 'this line'
		// if trat_ cegckbox is on {
			// find another data_limit_row
				// if that is also consumed use that lines fup 
				// else use this line's fup as main data limit 
		// }
		if($bw_applicable_row['treat_fup_as_dl_for_last_limit_row']){
			
			$next_data_limit_row = $this->getApplicableRow($now,null,$data_limit_row['id']);
			// echo "old id ".$data_limit_row['id']."<br/>";
			// echo "new id ".$next_data_limit_row['id']."<br/>";

			if( ($next_data_limit_row['download_data_consumed'] + $next_data_limit_row['upload_data_consumed']) > $next_data_limit_row['net_data_limit'] ){
				$data_limit_row['download_limit'] = $next_data_limit_row['fup_download_limit'];
				$data_limit_row['upload_limit'] = $next_data_limit_row['fup_upload_limit'];
				$data_limit_row['remark'] = $next_data_limit_row['remark'];
				// echo "next fup"."<br/>";
			}else{

				$data_limit_row['download_limit'] = $bw_applicable_row['fup_download_limit'];
				$data_limit_row['upload_limit'] = $bw_applicable_row['fup_upload_limit'];
				$data_limit_row['remark'] = $next_data_limit_row['remark'];
				// echo "old ".$next_data_limit_row['remark']."<br/>";
			}
		}

		$this->testDebug('Data Limit Crossed', $this->app->byte2human($data_limit_row['net_data_limit'] - ($data_limit_row['download_data_consumed'] + $data_limit_row['upload_data_consumed'])));
	}

	// Mark datalimitrow as effective
	$this->app->db->dsql()->table('isp_user_plan_and_topup')->set('is_effective',0)->where('user_id',$this->id)->update();
	$q=$this->app->db->dsql()->table('isp_user_plan_and_topup')->set('is_effective',1)->where('id',$data_limit_row['id']);
	$q->update();
	$this->testDebug('Mark Effecting for Next Accounting', $data_limit_row['remark'],['data_limit_row'=>$data_limit_row, 'query'=>$q->getDebugQuery($q->render())]);


	$dl_field = $if_fup.'download_limit';
	$ul_field = $if_fup.'upload_limit';

	// but from which row ??
	// from applicable if values exists
	$dl_limit = $bw_applicable_row[$dl_field];
	$ul_limit = $bw_applicable_row[$ul_field];

	if($dl_limit === null) $dl_limit = $data_limit_row[$dl_field];
	if($ul_limit === null) $ul_limit = $data_limit_row[$ul_field];
	// from data if not 
	// if fup is null or 0 it is a reject authentication command

	$access= true;
	if(!$dl_limit && !$ul_limit) $access=false;

	
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
	
	$final_row['coa'] = false;
	
	if(!$accounting_data OR ($accounting_data !==null && ($dl_limit !== $this['last_dl_limit'] || $ul_limit !== $this['last_ul_limit'] || !$access))){
		// echo "cur dl limit = ".$dl_limit." last dl limit = ".$this['last_dl_limit']."<br/>";
		// echo "cur ul limit = ".$dl_limit." last ul limit = ".$this['last_ul_limit']."<br/>";
		$final_row['coa'] = true;
		$this['last_dl_limit'] = $dl_limit;
		$this['last_ul_limit'] = $ul_limit;
		$this->save();
		$this->testDebug('Saving Dl/UL Limits', 'dl '.$dl_limit.', ul '. $ul_limit);
	}

	if($this['last_accounting_dl_ratio'] != $bw_applicable_row['accounting_download_ratio'] || $this['last_accounting_ul_ratio'] != $bw_applicable_row['accounting_upload_ratio']){
		$final_row['coa'] = true;
		$this['last_accounting_dl_ratio'] = $bw_applicable_row['accounting_download_ratio'];
		$this['last_accounting_ul_ratio'] = $bw_applicable_row['accounting_upload_ratio'];
		$this->testDebug('Saving Dl/UL Ratio for next accounting data', 'dl '.$bw_applicable_row['accounting_download_ratio'].', ul '. $bw_applicable_row['accounting_upload_ratio']);
		$this->save();
	}
		

	if($human_redable){
		$final_row['data_limit'] = $this->app->byte2human($final_row['data_limit']);
		$final_row['net_data_limit'] = $this->app->byte2human($final_row['net_data_limit']);
		$final_row['dl_limit'] = ($final_row['dl_limit'] !== null ) ? $this->app->byte2human($final_row['dl_limit']):null;
		$final_row['ul_limit'] = ($final_row['ul_limit'] !== null ) ? $this->app->byte2human($final_row['ul_limit']):null;
		$final_row['data_consumed'] = $this->app->byte2human($final_row['download_data_consumed'] + $final_row['upload_data_consumed']);
	}

	return ['access'=>$access, 'result'=>$final_row];
}