<?php

namespace xavoc\ispmanager;

class View_UserDataConsumption extends \View{
	public $username;

	function init(){
		parent::init();

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

		$rad_model = $this->add('xavoc\ispmanager\Model_RadAcctData');
		$rad_model->addCondition('username',$this->user['radius_username']);
		$rad_model->setOrder('radacctid','desc');
		$rad_model->_dsql()->group('year');
		$grid = $this->add('xepan\base\Grid');
		$grid->setModel($rad_model,['year','total_data_consumed','total_upload','total_download']);
		$grid->addColumn('total_data_consumed');

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
				$rad_model->setOrder('radacctid','desc');
				$rad_model->_dsql()->group('month_year');

				$grid = $page->add('xepan\base\Grid');
				$grid->setModel($rad_model,['month_year','total_data_consumed','total_upload','total_download']);
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
						$rad_model->setOrder('radacctid','desc');
						$rad_model->_dsql()->group('date');

						$grid = $day_page->add('xepan\base\Grid');
						$grid->setModel($rad_model,['date','total_data_consumed','total_upload','total_download']);
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
								$grid->setModel($rad_model,['acctsessionid','acctinputoctets','acctoutputoctets','callingstationid','framedipaddress','acctstarttime','acctupdatetime','acctstoptime']);
								$grid->addSno('');
								// $grid->addTotals(['total_upload','total_download']);
					 			$grid->addHook('formatRow',function($g){
									// $g->current_row_html['total_data_consumed'] = $this->app->byte2human($g->model['total_upload'] + $g->model['total_download']);
									$g->current_row_html['acctinputoctets'] = $this->app->byte2human($g->model['acctinputoctets']);
									$g->current_row_html['acctoutputoctets'] = $this->app->byte2human($g->model['acctoutputoctets']);
								});
				 			});
						
						$grid->addHook('formatRow',function($g){
							$g->current_row_html['total_data_consumed'] = $this->app->byte2human($g->model['total_upload'] + $g->model['total_download']);
							$g->current_row_html['total_upload'] = $this->app->byte2human($g->model['total_upload']);
							$g->current_row_html['total_download'] = $this->app->byte2human($g->model['total_download']);
						});		 			
		 			});

		 		$grid->addHook('formatRow',function($g){
					$g->current_row_html['total_data_consumed'] = $this->app->byte2human($g->model['total_upload'] + $g->model['total_download']);
					$g->current_row_html['total_upload'] = $this->app->byte2human($g->model['total_upload']);
					$g->current_row_html['total_download'] = $this->app->byte2human($g->model['total_download']);
				});
		});

 		$grid->addHook('formatRow',function($g){
			$g->current_row_html['total_data_consumed'] = $this->app->byte2human($g->model['total_upload'] + $g->model['total_download']);
			$g->current_row_html['total_upload'] = $this->app->byte2human($g->model['total_upload']);
			$g->current_row_html['total_download'] = $this->app->byte2human($g->model['total_download']);
		});

 		$grid->addPaginator(10);
 		// $grid->addTotals(['total_data_consumed','total_upload','total_download']);
 		// $grid->addHook('formatTotalsRow',function($g){
		// 	$g->current_row_html['total_data_consumed'] = $this->app->byte2human($g->model['total_data_consumed']);
		// });
	}
}