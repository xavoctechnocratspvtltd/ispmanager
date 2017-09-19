<?php

namespace xavoc\ispmanager;

/**
* 
*/
class page_log extends \xepan\base\Page{
	public $title = "SYS Log ";
	function init(){
		parent::init();

		$this->app->stickyGET('username');
		$from_date = $this->app->stickyGET('from_date')?:$this->app->today;
		$to_date = $this->app->stickyGET('to_date')?:$this->app->today;

		$skip_page = $this->app->stickyGET('pagintor');

		/*Mysql Credetial Configuration*/
		$db_m = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'host'=>'Line',
							'database_name'=>'Line',
							'database_username'=>'Line',
							'database_password'=>'password',
						],
					'config_key'=>'ISPMANAGER_SYSLOG_DATABASE_CONFIG',
					'application'=>'ispmanager'
			]);
		$db_m->tryLoadAny();

		$host = $db_m['host'];
		$username = $db_m['database_username'];
		$password = $db_m['database_password'];
		$database = $db_m['database_name'];

		$dsn = "mysql://$username:$password@$host/$database";

		
		/*Log Filter Form */
		$f = $this->add('Form');
		$f->add('xepan\base\Controller_FLC')
			->layout([
					'username'=>'Filter~c1~4',
					'from_date'=>'c2~2',
					'to_date'=>'c3~2',
					'FormButtons~'=>'c4~4',
				]);

		$user = $this->add('xavoc\ispmanager\Model_User');
		$user->title_field = "name_with_username";
		$user->addExpression('name_with_username')
					->set($user->dsql()
						->expr('CONCAT([0]," :: ",[1])',
												[
													$user->getElement('name'),
													$user->getElement('radius_username')
												]
							)
					)->sortable(true);

		$user_name_field = $f->addField('xepan\base\Basic','username');
		$user_name_field->setModel($user);
		$user_name_field->set($_GET['username']);

		$f->addField('DatePicker','from_date')->set($from_date);
		$f->addField('DatePicker','to_date')->set($to_date);

		$f->addSubmit('Get Detail')->addClass('btn btn-primary');

		$grid_view = $this->add('View');

		if(($skip_page - 1) >= 0)
			$previous_btn = $grid_view->add('Button')->set('previous')->js('click',$grid_view->js()->reload(['pagintor'=>($skip_page-1)]));
		// next btn
		$next_btn = $grid_view->add('Button')->set('next')->js('click',$grid_view->js()->reload(['pagintor'=>($skip_page+1)]));
		// previous btn


		$query = "SELECT * FROM SystemEvents Where Message LIKE '%->%'";
		if($_GET['username']){
			$filter_user = $this->add('xavoc\ispmanager\Model_User')->load($_GET['username']);
			$query .= " AND ( Message LIKE '%-".$filter_user['radius_username'].">%'". " OR SysLogTag Like '%".$filter_user['radius_username']."%'"." ) ";
		}

		if($from_date)
			$query .= " AND ReceivedAt >= '".$from_date."'";
		// else
		// 	$query .= " Where ReceivedAt >= '".$from_date."'";

		if($to_date)
			$query .= " AND ReceivedAt < '".$this->app->nextDate($to_date)."'";
		// else
		// 	$query .= " Where ReceivedAt < '".$this->app->nextDate($to_date)."'";

		if($skip_page)
			$query .= " order by id desc Limit 50,".($skip_page * 50).";";
		else
			$query .= " order by id desc Limit 50;";

		// echo $query;
		/*Access SysLog DB*/
		$new_db = $this->add('DB');
		$new_db->connect($dsn);
		$x = $new_db->dsql()->expr($query);//->get();
		
		$grid = $grid_view->add('Grid');
		// $grid->add('View',null,'grid_buttons')->set($filter_user['radius_username']);
		$grid->addColumn('username');
		// $grid->addColumn('SysLogTag');
		$grid->addColumn('Message');
		$grid->addColumn('ReceivedAt');
		// $grid->addColumn('from_ip');
		// $grid->addColumn('to_ip');
		$grid->setSource($x);

		$grid->addHook('formatRow',function($g){
			$temp = explode("->", $g->current_row['Message']);

			$from_temp = explode(',', $temp[0]);

			$from_temp = end($from_temp);
			$from_temp = explode(":", $from_temp);
			$from_ip = $from_temp[0];
			$from_port = $from_temp[1];

			$to_temp = explode(',', $temp[1]);
			$to_temp = $to_temp[0];

			$to_temp = explode(":", $to_temp);
			$to_ip = $to_temp[0];
			$to_port = $to_temp[1];

			$message = "<strong>From IP:</strong> ".$from_ip." <strong> From Port:</strong> ".$from_port;
			$message .= "<br/>"."<strong>To IP: </strong>".$to_ip."<strong> To Port: </strong>".$to_port;


			preg_match('/(.)*[<](.*)[>] (.*)/i', $g->current_row['Message'], $username);

			$g->current_row_html['Message'] = $message;

			$user_name = $username[2];
			if(!$user_name)
				$user_name = $g->current_row['SysLogTag'];

			$g->current_row_html['username'] = $user_name;
		});


		if($f->isSubmitted()){
			$f->js(null,$grid_view->js(null)->reload(['username'=>$f['username'],'from_date'=>$f['from_date'],'to_date'=>$f['to_date']]))->reload(['username'=>$f['username'],'from_date'=>$f['from_date'],'to_date'=>$f['to_date']])->execute();
		}


		// $grid->addColumn('CustomerID');
		// $grid->addColumn('ReceivedAt');
		// $grid->addColumn('DeviceReportedTime');
		// $grid->addColumn('Facility');
		// $grid->addColumn('Priority');
		// $grid->addColumn('FromHost');
		// $grid->addColumn('NTServerity');
		// $grid->addColumn('Importance');
		// $grid->addColumn('EventSource');
		// $grid->addColumn('EventUser');
		// $grid->addColumn('EventCategory');
		// $grid->addColumn('EventID');
		// $grid->addColumn('EventBinaryData');
		// $grid->addColumn('MaxAvailable');
		// $grid->addColumn('CurrUsage');
		// $grid->addColumn('MinUsage');
		// $grid->addColumn('MaxUsage');
		// $grid->addColumn('InfoUnitID');
		// $grid->addColumn('SysLogTag');
		// $grid->addColumn('EventLogType');
		// $grid->addColumn('GenericFileName');
		// $grid->addColumn('SystemID');


	}
}