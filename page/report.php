<?php

namespace xavoc\ispmanager;

class page_report extends \xepan\base\Page {
	
	public $title ="Reports";

	function page_index(){
		$tab = $this->add('Tabs');
		$tab->addTabUrl('./user','User');
		$tab->addTabUrl('./fupuser','User Under Fup');
		$tab->addTabUrl('./usercondition','UserCondition');
		$tab->addTabUrl('./fundprojection','Recurring Income Forcasting');
		$tab->addTabUrl('./missingno','Missing QSP No');
		$tab->addTabUrl('./useraudit','User Audit');

	}

	function page_useraudit(){
		$model = $this->add('xavoc\ispmanager\Model_UserAudit');
		$model->actions = [
				'Active'=>['view','CurrentConditions','personal_info','edit','delete'],
			];
		$model->addCondition('status','Active');
		$model->addCondition(
			$model->dsql()->orExpr()
					// ->where('actual_plan_condition','<>',$model->getElement('active_condition'))
					->where($model->getElement('is_last_condition_based_on_user_plan'),false)
					->where($model->getElement('active_condition'),'>',$model->getElement('actual_plan_condition'))
					->where($model->getElement('not_having_data_reset_value_count'),'>',0)
					->where(
						$model->dsql()->andExpr()
							->where($model->getElement('is_last_condition_active'),false)
							->where($model->getElement('active_condition'),'>=',$model->getElement('actual_plan_condition'))
						)
		);
		$model->getElement('plan')->caption('User Current Plan');
		$crud = $this->add('xepan\hr\CRUD',['allow_add'=>false]);
		$crud->setModel($model,null,['radius_username','plan','actual_plan_condition','active_condition','plan_last_condition_record_id','is_last_condition_based_on_user_plan','is_last_condition_active','not_having_data_reset_value_count']);
		$crud->grid->addPaginator(25);
		$crud->grid->removeAttachment();

		$crud->grid->addHook('formatRow',function($g){
			$g->current_row_html['is_last_condition_based_on_user_plan'] = $g->model['is_last_condition_based_on_user_plan']?'<b class="text-success">Yes</b>':'<b class="text-danger">No</b>';
			$g->current_row_html['is_last_condition_active'] = $g->model['is_last_condition_active']?'<b class="text-success">Yes</b>':'<b class="text-danger">No</b>';
		});

		// $crud->grid->add('View',null,'grid_heading_left')->setHtml('<b>Hint: in </b>');
	}


	function page_missingno(){
		$this->add('xepan\commerce\View_QSPMissingNo');
	}

	function page_fundprojection(){
		$filter = $this->app->stickyGET('filter');
		// $from_date = $this->app->stickyGET('from_date');
		$to_date = $this->app->stickyGET('to_date');

		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->showLables(true)
			->makePanelsCoppalsible(true)
			->layout([
					'to_date~Till Date'=>'Filter~c1~2',
					'FormButtons~&nbsp;'=>'c6~3'
				]);
		// $form->addField('DatePicker','from_date');
		$form->addField('DatePicker','to_date');
		$form->addSubmit('Filter');
		$v = $this->add('View');

		if($form->isSubmitted()){
			$form->js(null,$v->js()->reload([
					'filter'=>1,
					// 'from_date'=>$form['from_date'],
					'to_date'=>$form['to_date']
				]))->execute();
		}

		// logic
			// not expired
			// expression for diff day, month, week, year from end_date and to_date
			// expression on repeat_count based on plan_renewale value and unit
			// expression for multiple repeat_count * plan_amount
		$model = $this->add('xavoc\ispmanager\Model_UpcomingInvoices');
		$model->addCondition('is_expired','<>',true);
		if($filter){
			// if($from_date){
				// $model->addCondition('end_date','>=',$from_date);
			// }
			if($to_date){
				$model->to_date = $to_date;
				$model->addCondition('end_date','<=',$to_date);
			}
		}else
			$model->addCondition('id','-1');

		// $model->addCondition('days_count','>=',0);
		$model->addCondition('total_upcoming_invoice','>',0);
		$model->addCondition('is_topup',false);

		$model->setOrder('user_id');
		$grid = $v->add('xepan\base\Grid');
		$grid->add('View',null,'Pannel')->set('Report is under testing')->addClass('alert alert-info');

		$grid->setModel($model,['radius_username','user_status','plan','sale_price','plan_renewable_value','plan_renewable_unit','days_count','weeks_count','months_count','years_count','end_date','calculate_on_diff_var','total_upcoming_invoice','upcoming_invoice_amount','is_expired']);
		$grid->addPaginator(50);

		if($model->count()->getOne())
			$grid->addTotals(['s_no','total_upcoming_invoice','upcoming_invoice_amount']);

		$grid->addHook('formatRow',function($g){
			$g->current_row_html['plan'] = $g->model['plan']."<br/> Plan End Date: <b>".date('d-M-Y',strtotime($g->model['end_date']))."</b>";
			$g->totals['s_no'] = "Totals";
		});

		$removeColumn = ['is_expired','user_status','days_count','weeks_count','months_count','years_count','end_date'];
		foreach ($removeColumn as $key => $field_name) {
			$grid->removeColumn($field_name);
		}
	}


	function page_user(){

		$filter = $this->app->stickyGET('filter');
		$from_date = $this->app->stickyGET('from_date');
		$to_date = $this->app->stickyGET('to_date');
		$user_id = $this->app->stickyGET('user_id');
		$look_according_to = $this->app->stickyGET('look_according_to');
		$employee = $this->app->stickyGET('employee');

		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->showLables(true)
			->makePanelsCoppalsible(true)
			->layout([
					'from_date'=>'Filter~c1~2',
					'to_date'=>'c2~2',
					'look_according_to'=>'c3~2',
					'user~Radius User'=>'c4~3',
					'employee~User Created By Employee'=>'c5~3',
					'FormButtons~&nbsp;'=>'c6~3'
				]);
		$form->addField('DatePicker','from_date');
		$form->addField('DatePicker','to_date');

		$user_model = $this->add('xavoc\ispmanager\Model_User');
		$user_model->title_field = "radius_effective_name";
		$form->addField('autocomplete\Basic','user')
				->setModel($user_model);
		$look_field = $form->addField('DropDown','look_according_to')
				->setValueList(
					[
					'radius_user_created_at'=>'Created Date',
					'installation_assign_at'=>'Installation Assign Date',
					'installed_at'=>'Installed Date'
				]);
		$look_field->setEmptyText('select date variable');

		$form->addField('autocomplete\Basic','employee')->setModel('xepan\hr\Model_Employee');

		$submit_btn = $form->addSubmit('Filter');
		$clear_btn = $form->addSubmit('clear');

		$v = $this->add('View');
		if($form->isSubmitted()){
			if($form->isClicked($clear_btn)){
				$form->js(null,$v->js()->reload(['filter'=>0]))->univ()->reload()->execute();
			}

			$form->js(null,$v->js()->reload([
					'filter'=>1,
					'from_date'=>$form['from_date'],
					'to_date'=>$form['to_date'],
					'user_id'=>$form['user'],
					'look_according_to'=>$form['look_according_to'],
					'employee'=>$form['employee']
				]))->execute();
		}


		$model = $this->add('xavoc\ispmanager\Model_User');
		if($filter){
			if($user_id){
				$model->addCondition('id',$user_id);
			}else{
				if($from_date && $look_according_to){
					$model->addCondition($look_according_to,'>=',$from_date);
				}
				if($to_date && $look_according_to){
					$model->addCondition($look_according_to,'<',$this->app->nextDate($to_date));
				}

				if($look_according_to){
					$model->setOrder($look_according_to,'desc');
				}else{
					$model->setOrder('created_at','desc');
				}

				if($employee)
					$model->addCondition('created_by_id',$employee);
			}



		}else
			$model->addCondition('id','-1');

		$col = $v->add('Columns');
		$col1 = $col->addColumn(13);
		$col1->add('View')->addClass('alert alert-info')->set('Total Record: '.$model->count()->getOne());

		$grid = $v->add('xepan\base\Grid',['fixed_header'=>false]);
		$grid->setModel($model,['radius_effective_name','plan','created_at','installation_assign_at','installed_at','radius_user_created_at']);
		$grid->addPaginator(50);
		$grid->template->tryDel('quick_search_wrapper');
		$grid->addFormatter('radius_effective_name','Wrap');
		$grid->add('misc/Export');
	}

	function page_fupuser(){
		$to_date = $from_date = $this->app->today;

		$this->app->stickyGET('from_date');
		$this->app->stickyGET('to_date');
		$this->app->stickyGET('user_id');

		if($_GET['from_date'])
			$from_date = $_GET['from_date'];
		if($_GET['to_date'])
			$to_date = $_GET['to_date'];

		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->showLables(true)
			->makePanelsCoppalsible(true)
			->layout([
					'from_date'=>'Filter~c1~2',
					'to_date'=>'c2~2',
					'user'=>'c4~3',
					'FormButtons~&nbsp;'=>'c5~3'
				]);
		$form->addField('DatePicker','from_date')->set($from_date);
		$form->addField('DatePicker','to_date')->set($to_date);

		$user_model = $this->add('xavoc\ispmanager\Model_User');
		$user_model->title_field = "radius_effective_name";
		$form->addField('autocomplete\Basic','user')
				->setModel($user_model);
		$form->addSubmit('Filter');

		$v = $this->add('View');
		if($form->isSubmitted()){
			$form->js(null,$v->js()->reload(['from_date'=>$form['from_date'],'to_date'=>$form['to_date'],'user_id'=>$form['user'] ]))->execute();
		}

		$v->add('View')->set('User Under FUP From date: '.$from_date." To date".$to_date)->addClass('alert alert-info');

		$m = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
		$m->addExpression('user_status')->set($m->refSql('user_id')->fieldQuery('status'));
		$m->addCondition('data_consumed','>=',$m->getElement('net_data_limit'));
		$m->addCondition('net_data_limit','>',0);
		$m->addCondition('reset_date','>=',$from_date);
		$m->addCondition('reset_date','<',$this->app->nextDate($to_date));
		if($uid = $_GET['user_id'])
			$m->addCondition('user_id',$uid);

		$m->addCondition([['is_expired',false],['is_expired',null]]);
		$m->setOrder('reset_date','desc');

		$grid = $v->add('xepan\hr\Grid');
		$grid->setModel($m,['user','plan','remark','reset_date','net_data_limit','data_consumed','user_status']);
		$grid->addPaginator($ipp=50);
		$grid->template->tryDel('quick_search_wrapper');
		$grid->add('misc/Export');
	}

	function page_usercondition(){

		$crud = $this->add('CRUD',['allow_add'=>false]);

		if($crud->isEditing()){
			$form = $crud->form;
			$form->add('xepan\base\Controller_FLC')
				->addContentSpot()
				// ->makePanelsCoppalsible()
				->layout([
						'plan_id'=>'About Plan~c1~3',
						// 'condition_id'=>'c11~3',
						'remark'=>'c2~2',
						'is_topup'=>'c3~2',
						'data_limit'=>'c4~3',
						'carry_data'=>'c5~2',
						'download_limit'=>'DL/UL Limit~c1~3~in KBps',
						'upload_limit'=>'c11~3~in KBps',
						'fup_download_limit'=>'c12~3~in KBps',
						'fup_upload_limit'=>'c13~3~in KBps',
						'accounting_download_ratio'=>'c2~6~Ratio in %',
						'accounting_upload_ratio'=>'c21~6~Ratio in %',
						'start_date'=>'Dates~c1~3',
						'end_date'=>'c11~3',
						'expire_date'=>'c12~3',
						'is_expired'=>'c13~3',
						'is_recurring'=>'c2~3',
						'is_effective'=>'c21~3',
						'download_data_consumed'=>'Data Consumed~c1~6~in MB',
						'upload_data_consumed'=>'c2~6~in MB',
						'time_limit'=>'Time Limit~c1~3',
						'data_limit_row'=>'c11~3',
						'duplicated_from_record_id'=>'c12~3',
						'is_data_carry_forward'=>'c13~3',
						'start_time'=>'Time~c1~6',
						'end_time'=>'c2~6',
						'reset_date'=>'Reset Box~c1~3',
						'data_reset_value'=>'c2~3',
						'data_reset_mode'=>'c3~6',
						'sun'=>'Week~c1~1',
						'mon'=>'c2~1',
						'tue'=>'c3~1',
						'wed'=>'c4~1',
						'thu'=>'c5~1',
						'fri'=>'c6~1',
						'sat'=>'c7~1',
						'd01'=>'Days~c1~1',
						'd02'=>'c2~1',
						'd03'=>'c3~1',
						'd04'=>'c4~1',
						'd05'=>'c5~1',
						'd06'=>'c6~1',
						'd07'=>'c7~1',
						'd08'=>'c8~1',
						'd09'=>'c9~1',
						'd10'=>'c10~1',
						'd11'=>'c11~1',
						'd12'=>'c12~1',
						'd13'=>'c13~1',
						'd14'=>'c14~1',
						'd15'=>'c15~1',
						'd16'=>'c16~1',
						'd17'=>'c17~1',
						'd18'=>'c18~1',
						'd19'=>'c19~1',
						'd20'=>'c20~1',
						'd21'=>'c21~1',
						'd22'=>'c22~1',
						'd23'=>'c23~1',
						'd24'=>'c24~1',
						'd25'=>'c25~1',
						'd26'=>'c26~1',
						'd27'=>'c27~1',
						'd28'=>'c28~1',
						'd29'=>'c29~1',
						'd30'=>'c30~1',
						'd31'=>'c31~1',
						'treat_fup_as_dl_for_last_limit_row'=>'MISC~c1~6',
						'explanation'=>'c1~6',
						'is_pro_data_affected'=>'c2~6',
						'burst_dl_limit'=>'Burst~c1~3~limit per second',
						'burst_ul_limit'=>'c11~3~limit per second',
						'burst_threshold_dl_limit'=>'c12~3~limit per second',
						'burst_threshold_ul_limit'=>'c13~3~limit per second',
						'burst_dl_time'=>'c2~3~time in second',
						'burst_ul_time'=>'c21~3~time in second',
						'priority'=>'c22~6',
				]);
			
			$b = $form->layout->add('Button',null,'explanation')
				->set('explanation');
			$b->add('VirtualPage')
			->bindEvent('Explanation of treat fup as dl for last limit row','click')
			->set([$this,"explanation"]);

		}
		$model = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
		$model->addExpression('radius_username')->set($model->refSQL('user_id')->fieldQuery('radius_username'));
		// $model->addCondition('user_id',$this->id);
		$crud->setModel($model);
		$model->setOrder(['id desc','is_expired desc']);
		// if($crud->isEditing()){
		// 	$form = $crud->form;
		// 	$form->getElement('start_time')
		// 		->setOption('showMeridian',false)
		// 		->setOption('defaultTime',0)
		// 		->setOption('minuteStep',1)
		// 		->setOption('showSeconds',true)
		// 		;
		// 	$form->getElement('end_time')
		// 		->setOption('showMeridian',false)
		// 		->setOption('defaultTime',0)
		// 		->setOption('minuteStep',1)
		// 		->setOption('showSeconds',true)
		// 		;
		// }

		$crud->grid->addColumn('validity');
		$crud->grid->addColumn('detail');
		$crud->grid->addColumn('week_days');
		$crud->grid->addColumn('off_dates');
		$crud->grid->addColumn('burst_detail');
		$crud->grid->addPaginator(50);
		$crud->grid->addQuickSearch(['user','plan']);

		$crud->grid->addHook('formatRow',function($g){
			// data detail
			$speed = "UP/DL Limit: ".$g->model['upload_limit']."/".$g->model['download_limit']."<br/>";
			$speed .= "FUP UP/DL Limit: ".$g->model['fup_upload_limit']."/".$g->model['fup_download_limit']."<br/>";
			$speed .= "Accounting UP/DL Limit: ".$g->model['accounting_upload_ratio']."%/".$g->model['accounting_download_ratio']."%<br/>";
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
			$bt = "UL\DL Limit: ".$g->model['burst_ul_limit']."/".$g->model['burst_dl_limit']."<br/>";
			$bt .= "UL\DL Time: ".$g->model['burst_ul_time']."/".$g->model['burst_dl_time']."<br/>";
			$bt .= "Threshold UL\DL Time: ".$g->model['burst_threshold_ul_limit']."/".$g->model['burst_threshold_dl_limit']."<br/>";
			$bt .= "Priority: ".$g->model['priority'];
			$g->current_row_html['burst_detail'] = $bt;

			$detail = "Carry Data: ".$g->model['carry_data']."<br/>Condition Data: ".$g->model['data_limit']."<br/>Net Data: ".$g->model['net_data_limit']."<br/>"."Reset Every: ".($g->model['data_reset_value']." ".$g->model['data_reset_mode'])."<br/> Carried: ".$g->model['is_data_carry_forward']."<br/>";
			if(!$g->model['is_pro_data_affected'])
				$detail .= "<strong style='color:red;'>Pro Data Not Affected</strong>";
			else
				$detail .= "Pro Data Affected";

			$g->current_row_html['data_limit'] = $detail;

			// validity
			$g->current_row_html['validity'] = "Start Date: ".$g->model['start_date']."<br/>End Date: ".$g->model['end_date']."<br/>Expire Date: ".$g->model['expire_date']."<br/>Next Reset Date: ".$g->model['reset_date'];
			$g->current_row_html['remark'] = "<strong style='font-size:14px;'>".$g->model['plan']."</strong><br/>".$g->model['remark'].($g->model['is_topup']?"<strong style='color:red;'>TopUp</strong>":"").($g->model['is_expired']?('<br/><div class="label label-danger">Expired</div>'):"");
			// $g->current_row_html['data_consumed'] = $g->model['data_consumed'];

			if($g->model['is_effective']){
				$g->setTDParam('remark','class',"green-bg");
			}else
				$g->setTDParam('remark','class'," ");

			$g->current_row_html['user'] = $g->model['user']."<br/>".$g->model['radius_username'];
		});
		$removeColumn_list = [
					'condition','plan','upload_limit','download_limit','fup_download_limit','fup_upload_limit','accounting_upload_ratio','accounting_download_ratio',
					'sun','mon','tue','wed','thu','fri','sat','d01','d02','d03','d04','d05','d06','d07','d08','d09','d10','d11','d12','d13','d14','d15','d16','d17','d18','d19','d20','d21','d22','d23','d24','d25','d26','d27','d28','d29','d30','d31',
					'start_time','end_time','net_data_limit','carry_data',
					'data_reset_mode','data_reset_value','is_data_carry_forward',
					'burst_ul_limit','burst_dl_limit','burst_ul_time','burst_dl_time','burst_threshold_ul_limit','burst_threshold_dl_limit','priority',
					'treat_fup_as_dl_for_last_limit_row','is_pro_data_affected','action',
					'start_date','end_date','expire_date','is_topup','reset_date',
					'download_data_consumed','upload_data_consumed','time_limit','data_limit_row','duplicated_from_record_id',
					'is_recurring','is_effective','is_expired',
					'burst_detail','off_dates','radius_username'
				];
		foreach ($removeColumn_list as $field) {
			$crud->grid->removeColumn($field);
		}		
		// $crud->grid->removeAttachment();
		$o = $crud->grid->addOrder();
		$o->move('edit','first');
		$o->now();
		// $o->move('Delete','first')->now();

	}
}