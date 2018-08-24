<?php

namespace xavoc\ispmanager;

class page_test extends \xepan\base\Page {
	
	public $title ="Test Page";

	function init(){
		parent::init();

		// $user_name = 'vivekjain';
		// $start_date = '2018-08-01';
		// $end_date = '2018-09-01';
		$user_name = 'oscarspvtltd';
		$start_date = '2018-08-05';
		$end_date = '2018-09-05';


		// $user_name = 'virendrakumarjain';
		// $start_date = '2018-08-02';
		// $end_date = '2018-09-02';

		// $user_name = 'vishalmenariya';
		// $start_date = '2018-07-25';
		// $end_date = '2018-09-25';

		// $user_name = 'yogeshkachhwaha';
		// $start_date = '2018-07-21';
		// $end_date = '2018-08-24';

		$radacct = $this->add('xavoc\ispmanager\Model_RadAcct');
		$radacct->addCondition('username',$user_name);
		$radacct->addCondition('acctstarttime','>=',$start_date);
		$radacct->addCondition('acctstarttime','<',$end_date);
		$radacct->addCondition('acctstoptime','<>',NULL);

		$act_1 = $radacct->sum('acctinputoctets')->getOne();
		$act_2 = $radacct->sum('acctoutputoctets')->getOne();

		$col = $this->add('Columns');
		$col1 = $col->addColumn('4');
		$col2 = $col->addColumn('4');
		$col3 = $col->addColumn('4');

		$col1->add('View')->set("Closed Radius Session");
		$col1->add('View')->set($this->app->byte2human($act_1));
		$col1->add('View')->set($this->app->byte2human($act_2));
		$col1->add('View')->setHtml("<hr>Total: ".$this->app->byte2human($act_2 + $act_1));
		$col1->add('View')->setElement('hr');


		$model = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
		$model->addExpression('usernameradius')->set($model->refSQL('user_id')->fieldQuery('radius_username'));
		$model->addCondition('usernameradius',$user_name);
		$model->addCondition('is_effective',true);
		$model->setOrder('id','desc');

		$this->add('View')->set("User Condition data");
		$this->add('Grid')->setModel($model,['session_download_data_consumed','session_upload_data_consumed','session_download_data_consumed_on_reset','session_upload_data_consumed_on_reset','download_data_consumed','upload_data_consumed','data_consumed']);
		
		$acctmodel = $this->add('xavoc\ispmanager\Model_RadAcct');
		$acctmodel->addCondition('username',$user_name);
		$acctmodel->addCondition('acctstarttime','>=',$start_date);
		$acctmodel->addCondition('acctstarttime','<=',$end_date);
		$acctmodel->addCondition('acctstoptime',NULL);

		$act_11 = $acctmodel->sum('acctinputoctets')->getOne();
		$act_12 = $acctmodel->sum('acctoutputoctets')->getOne();

		$col2->add('View')->set("Un Closed Radius Session");
		$col2->add('View')->set($this->app->byte2human($act_11));
		$col2->add('View')->set($this->app->byte2human($act_12));
		$col2->add('View')->setHtml('<hr> Total: '.$this->app->byte2human($act_12+$act_11));

		$col3->add('View')->setHtml("Total: ".$this->app->byte2human($act_12+$act_11+$act_1+$act_2));
		// if($_GET['update_invoice']){
		// 	$m = $this->add('xepan\commerce\Model_SalesInvoice');
		// 	foreach ($m as $temp) {
		// 		$temp['serial']  = "PIPLB/2018-19/";
		// 		$temp->save();
		// 	}
		// }
		// $this->add('CRUD')->setModel($this->add('xavoc\ispmanager\Model_UserPlanAndTopup'));

		// $form = $this->add('Form');
		// $form->addField('DropDown', 'user')->setModel($this->add('xavoc\ispmanager\Model_User'));
		// $factor = ['daily_uses','monthly_uses','yearly_uses','day','date','time'];
		// foreach ($factor as $key => $value) {
		// 	$form->addField($value);
		// }

		// $form->addSubmit();
		
		// $view = $this->add('View')->addClass('main-box');
		// if($_GET['current_speed']){
		// 	$view->set('Current Speed = '.$_GET['current_speed']);
		// }


		// if($form->isSubmitted()){

		// 	$user_id = $form['user'];
		// 	$daily_uses = $form['daily_uses'];
		// 	$monthly_uses = $form['monthly_uses'];
		// 	$yearly_uses = $form['yearly_uses'];
		// 	$day = $form['day'];
		// 	$date = $form['date'];
		// 	$time = $form['time'];

		// 	$user_model = $this->add('xavoc\ispmanager\Model_User')->load($user_id);

		// 	$plan = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup')
		// 				->addCondition('user_id',$user_model['id'])
		// 				->addCondition('is_active',true)
		// 				;
			
		// 	// priority
		// 		// if topup 1000 + count of condition
		// 		// if plan 0 + count of condition
		// 	$plan->addExpression('priority')->set(function($m,$q){
		// 		return $q->expr('IF()');
		// 	});

		// 	$form->js(true,$view->js()->reload(['current_speed'=>$current_speed]))->execute();
		// }
		

		// $form = $this->add('Form');
		// $form->addSubmit('Send Lead Assign Message');
		// if($form->isSubmitted()){
		// 	$lead = $this->add('xavoc\ispmanager\Model_Lead');
		// 	$lead->addCondition('assign_to_id','<>','null');
		// 	$lead->setOrder('id','desc');
		// 	$lead->tryLoadAny();

		// 	$employee = $this->add('xepan\hr\Model_Employee');
		// 	$employee->addExpression('mobile_number')->set(function($m,$q){
		// 		$x = $m->add('xepan\base\Model_Contact_Phone');
		// 		return $x->addCondition('contact_id',$q->getField('id'))
		// 				->addCondition('is_active',true)
		// 				->addCondition('is_valid',true)
		// 				->setLimit(1)
		// 				->fieldQuery('value');
		// 	});

		// 	$employee->load($lead['assign_to_id']);
		// 	// send email and sms
		// 	$this->add('xavoc\ispmanager\Controller_Greet')->do($employee,'lead_assign',$lead);
			
		// 	$form->js()->univ()->successMessage('saved')->execute();
		// }
		
		// if($_GET['reset']){
		// 	$date = $_GET['date']?:$this->app->today;
		// 	$this->add('xavoc\ispmanager\Controller_ResetUserPlanAndTopup')->run($date);
		// }

	}
}