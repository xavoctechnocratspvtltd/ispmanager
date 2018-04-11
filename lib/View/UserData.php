<?php

namespace xavoc\ispmanager;

class View_UserData extends \View {
	public $isp_user_model;

	function init(){
		parent::init();

		$model = $this->add('xavoc\ispmanager\Model_UserData');
		$model->addCondition('user_id',$this->isp_user_model['user_id']);

		$model->getElement('country_id')->getModel('status','Active');
		$model->getElement('state_id')->getModel('status','Active');

		// return net_data_limit, data_consumed
		$model->addExpression('active_condition_data')->set(function($m,$q){
			$upt = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
			$upt->addCondition('plan_id',$m->getElement('plan_id'));
			$upt->addCondition('user_id',$m->getElement('id'));
			$upt->addCondition('is_effective',true);

			return $q->expr('CONCAT(IFNULL([0],0),",",IFNULL([1],0))',[
					$upt->fieldQuery('net_data_limit'),
					$upt->fieldQuery('data_consumed')
				]);
		});

		$model->addExpression('is_expired')->set(function($m,$q){
			$upt = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
			$upt->addCondition('plan_id',$m->getElement('plan_id'));
			$upt->addCondition('user_id',$m->getElement('id'));
			$upt->addCondition('is_effective',true);

			return $upt->fieldQuery('is_expired');
		});

		$model->addExpression('active_plan_end_date')->set(function($m,$q){
			$upt = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
			$upt->addCondition('plan_id',$m->getElement('plan_id'));
			$upt->addCondition('user_id',$m->getElement('id'));
			$upt->addCondition('is_effective',true);

			return $upt->fieldQuery('end_date');
		});

		$model->addExpression('framed_ip_address')->set(function($m,$q){
			$acc = $this->add('xavoc\ispmanager\Model_RadAcct');
			$acc->addCondition('username',$m->getElement('radius_username'));
			$acc->addCondition('acctstoptime',null);
			$acc->setOrder('radacctid','desc');
			$acc->setLimit(1);

			return $q->expr('IFNULL([0],"")',[$acc->fieldQuery('framedipaddress')]);
		});

		$model->getElement('emails_str')->caption('Emails');
		$model->getElement('contacts_str')->caption('Contacts');
		
		$this->add('View')->setElement('h3')->set('Current Plan');
		$crud = $this->add('xepan\base\CRUD',['entity_name'=>'User','allow_add'=>false,'allow_edit'=>false,'allow_del'=>false]);
		$crud->grid->fixed_header = false;
		$crud->grid->template->tryDel('quick_search_wrapper');

		$crud->setModel($model,['net_data_limit','radius_username','radius_password','plan_id','simultaneous_use','grace_period_in_days','custom_radius_attributes','first_name','last_name','create_invoice','is_invoice_date_first_to_first','include_pro_data_basis','country_id','state_id','city','address','pin_code','qty_unit_id','mac_address'],['name','radius_username','plan','radius_login_response','contacts_str','emails_str','created_at','last_login','is_online','active_condition_data','framed_ip_address','last_logout','name']);

		$crud->grid->removeColumn('attachment_icon');
		$crud->grid->removeColumn('framed_ip_address');
		$crud->grid->removeColumn('last_logout');
		$crud->grid->addPaginator($ipp=1);

		$crud->grid->addHook('formatRow',function($g){
			$data = explode(",",$g->model['radius_login_response']);
			$limits = explode("/", $data[2]);
			$ul_limit = $this->app->byte2human($limits[0]);
			$dl_limit = $this->app->byte2human($limits[1]);
			$data[2] = $ul_limit.' / '.$dl_limit;

			// data status 
			// find prosiible cause of no access 
			$access="Yes";
			
			if(!$data[0]){
				$access = "No Valid Plan Condition Matched: Authentication failed";
				if($g->model['is_expired'] || strtotime($this->app->now) > strtotime($g->model['active_plan_end_date'])) $access=" Plan Expired / Ended : Authentication Failed ";
			}

			$g->current_row_html['radius_login_response'] = 'Access: '.($data[0]?'yes':'<span class="label label-danger" style="font-size:8px;">'.$access.'</span>').'<br/>UL/ DL Upto: '.$data[2];
			
			// add online /offline
			$status = ($g->model['is_online'] && $data[0]) ? "Online":"Offline";
			$g->current_row_html['radius_username'] = $g->model['radius_username']."<br>".$g->model['name']."<br/><div class='".$status."'><i class='fa fa-circle'></i>&nbsp;".$status."</div><br>IP: ".$g->model['framed_ip_address'];


			$data_limit = explode(",",$g->model['active_condition_data']);
			$percentage = 0;
			if($data_limit[0] != 0){
				$percentage = ($data_limit[1] / $data_limit[0])*100;
			}

			$status_class = "green-bg";
			if($g->model['status'] != "InActive" AND ($data_limit[1] > $data_limit[0])){
				$status_class = "yellow-bg";
			}elseif($g->model['status'] == "InActive"){
				$status_class = "red-bg";
			}

			$progress = $this->add('AbstractController')->add('xepan\base\View_Widget_ProgressStatus',
					[
						'heading'=>$this->app->byte2human($data_limit[1]),
						'progress_percentage'=>$percentage,
						'value'=>round($percentage,0).'%',
						'footer'=>'Total: '.$this->app->byte2human($data_limit[0])
					]);
			$view_html = $progress->getHtml();
			$g->current_row_html['plan'] = $g->model['plan']."<br/>".$view_html;

			$g->setTDParam('radius_username','class',$status_class);

			$g->current_row_html['created_at'] = str_replace(" ", "<br/>", $g->model['created_at']);
			$g->current_row_html['last_login'] = "<small>last login </small><br/>".str_replace(" ", "<br/>", $g->model['last_login']).'<br/><small>last logout </small><br/>'.str_replace(" ", "<br/>", $g->model['last_logout']);
			$g->current_row_html['contacts_str'] = $g->model['contacts_str']."<br/>".$g->model['emails_str'];
		});

		$crud->grid->removeColumn('emails_str');
		$crud->grid->removeColumn('name');
		$crud->grid->addFormatter('contacts_str','Wrap');
		$crud->grid->addFormatter('radius_login_response','Wrap');
		$crud->grid->removeColumn('active_condition_data');
		$crud->grid->removeColumn('is_online');


		// add condition grid view 
		$this->add('View')->setElement('h3')->set('Active Plan Conditions');
		$condition_crud = $this->add('xepan\base\CRUD',['allow_add'=>false,'allow_del'=>false,'allow_edit'=>false]);
		$up_model = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
		$up_model->addCondition('user_id',$this->isp_user_model['id']);
		$model->addCondition([['is_expired',false],['is_expired',null]]);
		$condition_crud->setModel($up_model);
		$up_model->setOrder(['id desc','is_expired desc']);
		$condition_crud->grid->addPaginator(5);

		$condition_crud->grid->template->tryDel('quick_search_wrapper');
		$condition_crud->grid->addColumn('validity');
		$condition_crud->grid->addColumn('detail');
		$condition_crud->grid->addColumn('week_days');
		$condition_crud->grid->addColumn('off_dates');
		// $condition_crud->grid->addColumn('burst_detail');

		$condition_crud->grid->addHook('formatRow',function($g){
			// data detail
			$speed = "UP/DL Limit: ".$g->model['upload_limit']."/".$g->model['download_limit']."<br/>";
			$speed .= "FUP UP/DL Limit: ".$g->model['fup_upload_limit']."/".$g->model['fup_download_limit']."<br/>";
			// $speed .= "Accounting UP/DL Limit: ".$g->model['accounting_upload_ratio']."%/".$g->model['accounting_download_ratio']."%<br/>";
			$speed .= "start/end time: ".$g->model['start_time']."/".$g->model['end_time']."<br/>";
			if($g->model['treat_fup_as_dl_for_last_limit_row'])
				$speed .= "<strong style='color:red;'>FUP as DL for last limit row</strong><br/>";

			$speed .= "Time Limit: ".($g->model['time_limit']>0?($g->model['time_limit']." minutes"):"");
			$g->current_row_html['detail'] = $speed;
			
			$week_days = '';
			foreach (['sun','mon','tue','wed','thu','fri','sat'] as $name) {
				if($g->model[$name])
  					$week_days .= "<span style='color:green;'>".strtoupper(substr($name,0,1))."&nbsp;</span>";
  				else
  					$week_days .= "<span style='color:red;'>".strtoupper(substr($name,0,1))."&nbsp;</span>";
			}
			$g->current_row_html['week_days'] = $week_days;
			
			$week_days .= '</div>';

			$off_dates = "";
			foreach (['d01','d02','d03','d04','d05','d06','d07','d08','d09','d10','d11','d12','d13','d14','d15','d16','d17','d18','d19','d20','d21','d22','d23','d24','d25','d26','d27','d28','d29','d30','d31'] as $name) {
				if(!$g->model[$name])
					$off_dates .= trim($name,'d').",";
			}
			$g->current_row_html['off_dates'] = trim($off_dates,',');
			
			// burts detail
			// $bt = "UL\DL Limit: ".$g->model['burst_ul_limit']."/".$g->model['burst_dl_limit']."<br/>";
			// $bt .= "UL\DL Time: ".$g->model['burst_ul_time']."/".$g->model['burst_dl_time']."<br/>";
			// $bt .= "Threshold UL\DL Time: ".$g->model['burst_threshold_ul_limit']."/".$g->model['burst_threshold_dl_limit']."<br/>";
			// $bt .= "Priority: ".$g->model['priority'];
			// $g->current_row_html['burst_detail'] = $bt;

			$detail = "Carry Data: ".$g->model['carry_data']."<br/>Condition Data: ".$g->model['data_limit']."<br/>Net Data: ".$g->model['net_data_limit']."<br/>"."Reset Every: ".($g->model['data_reset_value']." ".$g->model['data_reset_mode'])."<br/> Carried: ".$g->model['is_data_carry_forward']."<br/>";
			if(!$g->model['is_pro_data_affected'])
				$detail .= "<strong style='color:red;'>Pro Data Not Affected</strong>";
			else
				$detail .= "Pro Data Affected";

			$g->current_row_html['data_limit'] = $detail;

			// validity
			$g->current_row_html['validity'] = "Start Date: ".date('Y-m-d',strtotime($g->model['start_date']))."<br/>End Date: ".date('Y-m-d',strtotime($g->model['end_date']))."<br/>Expire Date: ".date('Y-m-d',strtotime($g->model['expire_date']))."<br/>Next Reset Date: ".date('Y-m-d',strtotime($g->model['reset_date']));
			$g->current_row_html['remark'] = "<strong style='font-size:14px;'>".$g->model['plan']."</strong><br/>".$g->model['remark'].($g->model['is_topup']?"<strong style='color:red;'>TopUp</strong>":"").($g->model['is_expired']?('<br/><div class="label label-danger">Expired</div>'):"");
			// $g->current_row_html['data_consumed'] = $g->model['data_consumed'];

			if($g->model['is_effective']){
				$g->setTDParam('remark','class',"green-bg");
			}else
				$g->setTDParam('remark','class'," ");

		});
		$removeColumn_list = [
					'user_id','user','condition','plan','upload_limit','download_limit','fup_download_limit','fup_upload_limit','accounting_upload_ratio','accounting_download_ratio',
					'sun','mon','tue','wed','thu','fri','sat','d01','d02','d03','d04','d05','d06','d07','d08','d09','d10','d11','d12','d13','d14','d15','d16','d17','d18','d19','d20','d21','d22','d23','d24','d25','d26','d27','d28','d29','d30','d31',
					'start_time','end_time','net_data_limit','carry_data',
					'data_reset_mode','data_reset_value','is_data_carry_forward',
					'burst_ul_limit','burst_dl_limit','burst_ul_time','burst_dl_time','burst_threshold_ul_limit','burst_threshold_dl_limit','priority',
					'treat_fup_as_dl_for_last_limit_row','is_pro_data_affected','action',
					'start_date','end_date','expire_date','is_topup','reset_date',
					'download_data_consumed','upload_data_consumed','time_limit','data_limit_row','duplicated_from_record_id',
					'is_recurring','is_effective','is_expired'
				];
		foreach ($removeColumn_list as $field) {
			$condition_crud->grid->removeColumn($field);
		}		
		$condition_crud->grid->removeAttachment();

		// session uses
		$this->add('View')->setElement('h3')->set('Session Uses');
		$radacct = $this->add('xavoc\ispmanager\Model_RadAcct');
		$radacct->addCondition('username',$this->isp_user_model['radius_username']);
		$radacct->setOrder('radacctid','desc');
		$radacct->add('xavoc\ispmanager\Controller_HumanByte')
			->handleFields([
				'acctinputoctets',
				'acctoutputoctets'
			]);
		$radacct->getElement('acctoutputoctets')->caption('Download Data');
		$radacct->getElement('acctinputoctets')->caption('Upload Data');
		$radacct->getElement('acctstarttime')->caption('Start Time');
		$radacct->getElement('acctstoptime')->caption('Stop Time');
		$radacct->getElement('acctupdatetime')->caption('Last Update Time');
		$grid = $this->add('Grid');
		$grid->setModel($radacct,['acctstarttime','acctstoptime','acctupdatetime','acctinputoctets','acctoutputoctets','framedipaddress']);
		$grid->addPaginator(5);
		$grid->template->tryDel('quick_search_wrapper');
	}
}