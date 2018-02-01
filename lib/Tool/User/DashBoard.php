<?php

namespace xavoc\ispmanager;

/**
* 
*/
class Tool_User_DashBoard extends \xepan\cms\View_Tool{
	public $options = [
		'login_url'=>'hotspotlogin',
		'hotspot_base_url'=>'http://isp.prompthotspot.com'
		// 'nas_ip'=>'103.89.255.86', // using link-login from MT
		
	];

	function init(){
		parent::init();
		
		if($this->owner instanceof \AbstractController) return;
		
		if(!$this->app->auth->isLoggedIn()){
			$this->app->redirect($this->app->url($this->options['login_url']));
			return;
		}

		$this->user = $user = $this->add('xavoc\ispmanager\Model_User');
		$user->loadLoggedIn();

		$plan = $this->add('xavoc\ispmanager\Model_Plan')
			->addCondition('id',$user['plan_id'])
			->tryLoadAny();

		if($_GET['error']){
			$this->add('View')->set($_GET['error']);
			return;
		}
		
		// if($ll=$_GET['link-login'] OR $ll = $this->app->recall('link-login') ){
		// 	$this->app->memorize('link-login',$ll);

			if(!$this->app->recall('isLoggedIn',false)){
				$ll = $this->options['hotspot_base_url']."/login";

				$this->add('View')->setHTML("
						<form name='redirect' action='$ll'>
							<input type='hidden' name='username' value='".$user['radius_username']."' />
							<input type='hidden' name='password' value='".$user['radius_password']."' />
						</form>
						<script>
							document.redirect.submit();
						</script>
					");
				$this->app->memorize('isLoggedIn',true);
			}
		// }

		// echo "string". $user['plan_id'];
		// $user = $this->app->auth->model;



		$this->template->set('plan',$plan['name']);
		$this->template->set('username',$this->app->auth->model['username']);

		$list = $user->getCurrentCondition();
		$dw_t_consumed = 0;
		$up_t_consumed = 0;
		$total_data_limit = 0;
		$total_data_consumed = 0;
		foreach ($list as $key => $condition) {
			$up_t_consumed += $condition['upload_data_consumed']?:0 + $condition['session_upload_data_consumed']?:0;
			$dw_t_consumed += $condition['download_data_consumed']?:0 + $condition['session_download_data_consumed']?:0;
			$total_data_consumed += $condition['data_consumed']?:0;
			$total_data_limit += $condition['data_limit']?:0;
		}

		$this->template->trySet('consume_data',$user->byte2human($total_data_consumed));
		$this->template->trySet('total_data_limit',$user->byte2human($total_data_limit));

		$this->dataconsumed();
	}

	function defaultTemplate(){
		return ['view/user-dashboard'];
	}

	function dataconsumed(){

		$rad_model = $this->add('xavoc\ispmanager\Model_RadAcctData');
		$rad_model->addCondition('username',$this->user['radius_username']);
		$rad_model->setOrder('radacctid','desc');
		$rad_model->_dsql()->group('year');
		// $rad_model->debug();
		$grid = $this->add('Grid');
		$grid->setModel($rad_model,['year','total_data','total_upload','total_download']);
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

				$grid = $page->add('Grid');
				$grid->setModel($rad_model,['month_year','total_data','total_upload','total_download']);
				$grid->addTotals(['total_upload','total_download']);
				
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

						$grid = $day_page->add('Grid');
						$grid->setModel($rad_model,['date','total_data','total_upload','total_download']);
						$grid->addTotals(['total_upload','total_download']);

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

								$grid = $session_page->add('Grid');
								$grid->setModel($rad_model,['date','total_data','total_upload','total_download']);
								$grid->addTotals(['total_upload','total_download']);
				 			});
		 			});
		});

	}
}