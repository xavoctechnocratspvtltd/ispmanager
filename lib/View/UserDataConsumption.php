<?php

namespace xavoc\ispmanager;

class View_UserDataConsumption extends \View{
	public $username;

	public $start_date;
	public $end_date;
	function init(){
		parent::init();

		$filter = $this->app->stickyGET('filter');
		$this->start_date = $this->app->stickyGET('start_date');
		$this->end_date = $this->app->stickyGET('end_date');

		$this->user = $user = $this->add('xavoc\ispmanager\Model_User');
		if($this->username){
			$this->user->addCondition('radius_username',$this->username);
			$this->user->tryLoadAny();
		}else{
			$user->loadLoggedIn();
		}

		if(!$user->loaded()){
			$this->add('View')->set("ISP User is not loaded")->addClass('alert alert-danger');
			return;
		}

		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->showLables(true)
			->makePanelsCoppalsible(true)
			->layout([
					'date_range'=>'Filter~c1~6',
					'FormButtons~&nbsp;'=>'c2~6'
				]);

		$dr_fld = $form->addField('DateRangePicker','date_range')
                // ->showTimer(15)
                ->getBackDatesSet() // or set to false to remove
                // ->getFutureDatesSet() // or skip to not include
                ;
        $form->addSubmit('Filter')->addClass('btn btn-primary');

		$rad_model = $this->add('xavoc\ispmanager\Model_RadAcctData');
		$rad_model->addCondition('username',$this->user['radius_username']);
		if($this->start_date)
			$rad_model->addCondition('acctstarttime','>=',$this->start_date);
		if($this->end_date)
			$rad_model->addCondition('acctupdatetime','<',$this->app->nextDate($this->end_date));

		$rad_model->setOrder('radacctid','desc');
		$rad_model->_dsql()->group('year');


		$grid = $this->add('xepan\base\Grid');
		$grid->setModel($rad_model,['year','total_data_consumed','total_upload','total_download','total_duration_in_sec','total_duration_in_hms']);
		$grid->addColumn('total_data_consumed');

		// filter form
        if($form->isSubmitted()){
        	$grid->js()->reload(['filter'=>1,'start_date'=>$dr_fld->getStartDate(),'end_date'=>$dr_fld->getEndDate()])->execute();
        	// throw new \Exception($dr_fld->getStartDate()." = ".$dr_fld->getEndDate());
        }

		$grid->add('View',null,'Pannel')->setElement('h3')->set('User Data Consumption');
		$grid->add('VirtualPage')
			->addColumn('month')
 			->set(function($page){

				$id = $_GET[$page->short_name.'_id'];
				$selected_row = $this->add('xavoc\ispmanager\Model_RadAcctData');
				$selected_row->load($id);

				$rad_model = $this->add('xavoc\ispmanager\Model_RadAcctData');
				$rad_model->addCondition('year',$selected_row['year']);
				$rad_model->addCondition('username',$this->user['radius_username']);

				if($this->start_date)
					$rad_model->addCondition('acctstarttime','>=',$this->start_date);
				if($this->end_date)
					$rad_model->addCondition('acctupdatetime','<',$this->app->nextDate($this->end_date));

				$rad_model->setOrder('radacctid','desc');
				$rad_model->_dsql()->group('month_year');

				$grid = $page->add('xepan\base\Grid');
				$grid->setModel($rad_model,['month_year','total_data_consumed','total_upload','total_download','total_duration_in_sec','total_duration_in_hms']);
				$grid->addColumn('total_data_consumed');
				// $grid->addTotals(['total_upload','total_download']);
				$grid->add('VirtualPage')
					->addColumn('Days')
		 			->set(function($day_page){

		 				$id = $_GET[$day_page->short_name.'_id'];
		 				$temp = $this->add('xavoc\ispmanager\Model_RadAcctData');
		 				$temp->load($id);
		 				
		 				$rad_model = $this->add('xavoc\ispmanager\Model_RadAcctData');
						$rad_model->addCondition('year',$temp['year']);
						$rad_model->addCondition('month_year',$temp['month_year']);
						$rad_model->addCondition('username',$this->user['radius_username']);

						if($this->start_date)
							$rad_model->addCondition('acctstarttime','>=',$this->start_date);
						if($this->end_date)
							$rad_model->addCondition('acctupdatetime','<',$this->app->nextDate($this->end_date));

						$rad_model->setOrder('radacctid','desc');
						$rad_model->_dsql()->group('date');

						$grid = $day_page->add('xepan\base\Grid');
						$grid->setModel($rad_model,['date','total_data_consumed','total_upload','total_download','total_duration_in_sec','total_duration_in_hms']);
						$grid->addColumn('total_data_consumed');
						// $grid->addTotals(['total_upload','total_download']);

						$grid->add('VirtualPage')
							->addColumn('session')
				 			->set(function($session_page){

				 				$id = $_GET[$session_page->short_name.'_id'];
				 				$temp = $this->add('xavoc\ispmanager\Model_RadAcctData');
				 				$temp->load($id);
				 				
				 				$rad_model = $this->add('xavoc\ispmanager\Model_RadAcctData');
								$rad_model->addCondition('date',$temp['date']);
								$rad_model->addCondition('username',$this->user['radius_username']);

								if($this->start_date)
									$rad_model->addCondition('acctstarttime','>=',$this->start_date);
								if($this->end_date)
									$rad_model->addCondition('acctupdatetime','<',$this->app->nextDate($this->end_date));
								
								$rad_model->setOrder('radacctid','desc');

								$rad_model->getElement('acctinputoctets')->caption('Upload Data');
								$rad_model->getElement('acctoutputoctets')->caption('Download Data');
								$rad_model->getElement('acctstarttime')->caption('Session Start Time');
								$rad_model->getElement('acctstoptime')->caption('Session Stop Time');
								$rad_model->getElement('acctupdatetime')->caption('Session Update Time');
								$rad_model->getElement('acctsessionid')->caption('Session Id');
								$rad_model->getElement('callingstationid')->caption('Mac Address');
								$rad_model->getElement('framedipaddress')->caption('IP Address');

								$grid = $session_page->add('xepan\base\Grid');
								$grid->setModel($rad_model,['acctsessionid','acctinputoctets','acctoutputoctets','callingstationid','framedipaddress','acctstarttime','acctupdatetime','acctstoptime','duration_in_sec','duration_in_hms']);
								$grid->addSno('');
								// $grid->addTotals(['total_upload','total_download']);
					 			$grid->addHook('formatRow',function($g){
									// $g->current_row_html['total_data_consumed'] = $this->app->byte2human($g->model['total_upload'] + $g->model['total_download']);
									$g->current_row_html['acctinputoctets'] = $this->app->byte2human($g->model['acctinputoctets']);
									$g->current_row_html['acctoutputoctets'] = $this->app->byte2human($g->model['acctoutputoctets']);
								});

								$grid->removeColumn('duration_in_sec');
								$grid->addFormatter('acctstarttime','Wrap');
								$grid->addFormatter('acctstoptime','Wrap');
								$grid->addFormatter('acctupdatetime','Wrap');
				 			});
						
						$grid->addHook('formatRow',function($g){
							$g->current_row_html['total_data_consumed'] = $this->app->byte2human($g->model['total_upload'] + $g->model['total_download']);
							$g->current_row_html['total_upload'] = $this->app->byte2human($g->model['total_upload']);
							$g->current_row_html['total_download'] = $this->app->byte2human($g->model['total_download']);
						});
						$grid->removeColumn('total_duration_in_sec');
		 			});

		 		$grid->addHook('formatRow',function($g){
					$g->current_row_html['total_data_consumed'] = $this->app->byte2human($g->model['total_upload'] + $g->model['total_download']);
					$g->current_row_html['total_upload'] = $this->app->byte2human($g->model['total_upload']);
					$g->current_row_html['total_download'] = $this->app->byte2human($g->model['total_download']);
				});
				$grid->removeColumn('total_duration_in_sec');
		});

 		$grid->addHook('formatRow',function($g){
			$g->current_row_html['total_data_consumed'] = $this->app->byte2human($g->model['total_upload'] + $g->model['total_download']);
			$g->current_row_html['total_upload'] = $this->app->byte2human($g->model['total_upload']);
			$g->current_row_html['total_download'] = $this->app->byte2human($g->model['total_download']);
		});

 		$grid->addPaginator(10);
 		$grid->removeColumn('total_duration_in_sec');
 		// $grid->addTotals(['total_data_consumed','total_upload','total_download']);
 		// $grid->addHook('formatTotalsRow',function($g){
		// 	$g->current_row_html['total_data_consumed'] = $this->app->byte2human($g->model['total_data_consumed']);
		// });
	}
}