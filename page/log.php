<?php

namespace xavoc\ispmanager;

/**
* 
*/
class page_log extends \xepan\base\Page{
	public $title = "SYS Log ";
	function init(){
		parent::init();

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

		$f->addField('xepan\base\Basic','username')->setModel($user);
		$f->addSubmit('Get Detail')->addClass('btn btn-primary');

		$grid_view = $this->add('View');
		
		if($_GET['username']){
			$filter_user = $this->add('xavoc\ispmanager\Model_User')->load($_GET['username']);
			/*Access SysLog DB*/
			$new_db = $this->add('DB');
			$new_db->connect($dsn);
			$query = "SELECT * FROM SystemEvents WHERE Message LIKE '%-".$filter_user['radius_username']."%';";
			$x = $new_db->dsql()->expr($query);//->get();
			$grid = $grid_view->add('Grid');
			$grid->add('View',null,'grid_buttons')->set($filter_user['radius_username']);
			$grid->addColumn('Message');
			$grid->setSource($x);
			// $grid->addPaginator(20);
		}

		

		if($f->isSubmitted()){
			
			$f->js(null,$grid_view->js()->reload(['username'=>$f['username']]))->reload()->execute();
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