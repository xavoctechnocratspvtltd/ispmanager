<?php

namespace xavoc\ispmanager;

class page_user extends \xepan\base\Page {
	
	public $title ="User";

	function page_index(){
		// parent::init();
			
		$model = $this->add('xavoc\ispmanager\Model_UserData');
		$model->getElement('country_id')->getModel('status','Active');
		$model->getElement('state_id')->getModel('status','Active');

		// return net_data_limit, data_consumed
		$model->addExpression('active_condition_data')->set(function($m,$q){
			$upt = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
			$upt->addCondition('plan_id',$m->getElement('plan_id'));
			$upt->addCondition('user_id',$m->getElement('id'));
			$upt->addCondition('is_effective',true);
			$upt->setOrder('id','desc');
			$upt->setLimit(1);

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
			$upt->setOrder('id','desc');
			$upt->setLimit(1);

			return $upt->fieldQuery('is_expired');
		});

		$model->addExpression('active_plan_end_date')->set(function($m,$q){
			$upt = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
			$upt->addCondition('plan_id',$m->getElement('plan_id'));
			$upt->addCondition('user_id',$m->getElement('id'));
			$upt->addCondition('is_effective',true);
			$upt->setOrder('id','desc');
			$upt->setLimit(1);

			return $upt->fieldQuery('end_date');
		});

		$model->addExpression('active_plan_expire_date')->set(function($m,$q){
			$upt = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
			$upt->addCondition('plan_id',$m->getElement('plan_id'));
			$upt->addCondition('user_id',$m->getElement('id'));
			$upt->addCondition('is_effective',true);
			$upt->setOrder('id','desc');
			$upt->setLimit(1);

			return $upt->fieldQuery('expire_date');
		});

		$model->addExpression('framed_ip_address')->set(function($m,$q){
			$acc = $this->add('xavoc\ispmanager\Model_RadAcct');
			$acc->addCondition('username',$m->getElement('radius_username'));
			$acc->addCondition('acctstoptime',null);
			$acc->setOrder('radacctid','desc');
			$acc->setLimit(1);

			return $q->expr('IFNULL([0],"")',[$acc->fieldQuery('framedipaddress')]);
		});

		$model->addExpression('due_date')->set(function($m,$q){
			$up = $m->add('xavoc\ispmanager\Model_UserPlanAndTopup');
			$up->addCondition('user_id',$m->getElement('id'));
			$up->addCondition('plan_id',$m->getElement('plan_id'));
			$up->addCondition('is_effective',true);
			$up->addCondition([['is_expired',false],['is_expired',null]]);
			$up->setOrder('id','desc');
			$up->setLimit(1);
			return $q->expr('IFNULL([0],NULL)',[$up->fieldQuery('end_date')]);
		})->type('date');


		if($s = $_GET['status']){
			$model->addCondition('status',$s);
		}else{
			$model->addCondition('status',['Active','InActive']);
		}

		$model->add('xepan\base\Controller_SideBarStatusFilter',['add_status_to_sidebar'=>['Active','InActive','InDemo']]);

		$model->is([
				'radius_username|to_trim|required',
				'radius_password|to_trim|required',
				'plan_id|required',
				'first_name|to_trim|required',
				'last_name|to_trim|required',
				'country_id|required',
				'state_id|required',
				'city|required',
				'pin_code|required',
				'address|required'
			]);

		$model->addHook('afterSave',[$model,'updateUserConditon']);
		$model->addHook('afterSave',[$model,'createInvoice'],[null,null,$this->app->now]);
		$model->addHook('afterSave',[$model,'updateNASCredential']);
		$model->addHook('afterSave',[$model,'updateWebsiteUser']);
		$model->setOrder('id','desc');

		$model->getElement('emails_str')->caption('Emails');
		$model->getElement('contacts_str')->caption('Contacts');
		

		$crud = $this->add('xepan\hr\CRUD',['entity_name'=>'User']);
		$crud->grid->fixed_header = false;
		$crud->grid->add('misc\Export');
		if($crud->isEditing()){
			$form = $crud->form;

			$form->add('xepan\base\Controller_FLC')
				->showLables(true)
				->addContentSpot()
				->makePanelsCoppalsible(true)
				->layout([
						'first_name'=>'User Information~c1~6',
						'last_name'=>'c2~6',
						'address'=>'c7~12',
						'country_id'=>'c3~3',
						'state_id'=>'c4~3',
						'city'=>'c5~3',
						'pin_code'=>'c6~3',
						
						'plan~Plan'=>'User Plan Information~c1~4',
						'radius_username~Radius User Name'=>'c2~4',
						'radius_password'=>'c3~4',
						'simultaneous_use'=>'c4~4',
						'grace_period_in_days'=>'c5~4',
						'mac_address'=>'c6~4',
						// 'custom_radius_attributes'=>'c7~12',
						'create_invoice~'=>'c8~12',
						'is_invoice_date_first_to_first~'=>'c9~12',
						'include_pro_data_basis'=>'c10~12'
					]);
			// $form->setLayout('form/user');
		}

		$crud->setModel($model,['net_data_limit','branch_id','radius_username','radius_password','plan_id','simultaneous_use','grace_period_in_days','custom_radius_attributes','first_name','last_name','create_invoice','is_invoice_date_first_to_first','include_pro_data_basis','country_id','state_id','city','address','pin_code','qty_unit_id','mac_address'],['name','radius_username','plan','radius_login_response','contacts_str','emails_str','created_at','last_login','is_online','active_condition_data','framed_ip_address','last_logout','name','created_by','active_plan_expire_date','due_date']);

		$crud->grid->removeColumn('attachment_icon');
		$crud->grid->removeColumn('framed_ip_address');
		$crud->grid->removeColumn('last_logout');

		$crud->grid->addPaginator($ipp=10);
		$filter_form = $crud->grid->addQuickSearch(['name','radius_username','plan']);
		$crud->grid->addSno();

		if($crud->isEditing()){
			$form = $crud->form;
			$date_to_date_field = $form->getElement('is_invoice_date_first_to_first');
			$date_to_date_field->js(true)->univ()->bindConditionalShow([
				'1'=>['include_pro_data_basis']
			],'div.atk-form-row');

			$country_field = $form->getElement('country_id');
			$country_field->getModel()->addCondition('status','Active');
			$country_field->set(100);

			$state_field = $form->getElement('state_id');
			$state_field->getModel()->addCondition('status','Active');

			$country_id = $this->app->stickyGET('country_id');

			$country_field->js('change',$state_field->js()->reload(null,null,[$this->app->url(null,['cut_object'=>$state_field->name]),'country_id'=>$country_field->js()->val()]));
			if($country_id){
				$state_field->getModel()->addCondition('country_id',$country_id);
			}else{
				$state_field->set(95);
			}

			$form->getElement('create_invoice')->set(false);
			$form->getElement('is_invoice_date_first_to_first')->set(false);
		}

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

			$g->current_row_html['radius_login_response'] = 'Access: '.($data[0]?'<span class="label label-success">yes</span>':'<br/><span class="label label-danger" style="font-size:8px;">'.$access.'</span>').'<br/>'.'COA: '.($data[1]?'yes':'no').'<br/>UL / DL: '.$data[2].'<br/>Burst: '.$data['3']."<br/>Expire Date: ".($g->model['active_plan_expire_date']?date('d-M-Y',strtotime($g->model['active_plan_expire_date'])):"");
			
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

			$g->current_row_html['created_at'] = str_replace(" ", "<br/>", $g->model['created_at'])."<br/>".$g->model['created_by'];
			$g->current_row_html['last_login'] = "<small>last login </small><br/>".str_replace(" ", "<br/>", $g->model['last_login']).'<br/><small>last logout </small><br/>'.str_replace(" ", "<br/>", $g->model['last_logout']);
			$g->current_row_html['contacts_str'] = $g->model['contacts_str']."<br/>".$g->model['emails_str'];
		});

		$crud->grid->removeColumn('emails_str');
		$crud->grid->removeColumn('name');
		$crud->grid->addFormatter('contacts_str','Wrap');
		$crud->grid->addFormatter('radius_login_response','Wrap');
		$crud->grid->removeColumn('active_condition_data');
		$crud->grid->removeColumn('is_online');
		$crud->grid->removeColumn('created_by');
		$crud->grid->removeColumn('active_plan_expire_date');
		// $g->addMethod('format_redgreen',function($g,$f){
		// 	if($g->model['status']=='Red'){
		// 		$g->setTDParam($f,'style/color','red');
		// 	}else{
		// 		$g->setTDParam($f,'style/color','');
		// 	}
		// });
		// $g->addFormatter('user','redgreen');

		$import_btn = $crud->grid->addButton('Import CSV')->addClass('btn btn-primary');
		$import_btn->setIcon('fa fa fa-arrow-up');

		$import_btn->js('click')
			->univ()
			->frameURL(
					'Import CSV',
					$this->app->url('./import')
					);

		// filter form  add city column
		$data = $this->app->db->dsql()->expr('SELECT DISTINCT(city) AS city FROM contact')->get();
		$city_list = [];
		foreach ($data as $key => $value) {
			if(!trim($value['city'])) continue;
			$city_list[$value['city']] = $value['city'];
		}
		$city_field = $filter_form->addField('DropDown','filter_city');
		$city_field->setValueList($city_list);
		$city_field->setEmptyText('Select City to filter');

		$connection_status_field = $filter_form->addField('DropDown','user_connection_status');
		$connection_status_field->setValueList(['Online'=>'Online','Offline'=>'Offline']);
		$connection_status_field->setEmptyText('Select Connection Status');

		// tag filter
		$alltag = $this->add('xepan\base\Model_Contact_Tag')->getAllTag();
		$tag_field = $filter_form->addField('DropDown','tag');
		$tag_field->setValueList($alltag)->setEmptyText('Select Tags');

		$filter_form->addHook('applyFilter',function($f,$m){
			if($f['filter_city']){
				$m->addCondition('city',$f['filter_city']);
			}

			if($status = $f['user_connection_status']){
				if($status == "Online")
					$m->addCondition('is_online',true);
				if($status == "Offline")
					$m->addCondition('is_online',false);
			}

			if($f['tag']){
				$m->addCondition('tag','like','%'.$f['tag'].'%');
			}

		});

		$city_field->js('change',$filter_form->js()->submit());
		$connection_status_field->js('change',$filter_form->js()->submit());
		$tag_field->js('change',$filter_form->js()->submit());
	}

	function page_import(){
		
		$col = $this->add('Columns');
		$col1 = $col->addColumn('6')->addClass('col-md-6 col-lg-6 col-sm-12');
		$col2 = $col->addColumn('6')->addClass('col-md-6 col-lg-6 col-sm-12');

		$form = $col1->add('Form');
		$form->addSubmit('Download Sample File')->addClass('btn btn-primary');
		
		if($_GET['download_sample_csv_file']){
			$output = ['RADIUS_USERNAME','RADIUS_PASSWORD','PLAN','SIMULTANEOUS_USE','GRACE_PERIOD_IN_DAYS','FIRST_NAME','LAST_NAME','COUNTRY','STATE','CITY','ADDRESS','PIN_CODE','CREATE_INVOICE','INVOICE_DATE','IS_INVOICE_DATE_FIRST_TO_FIRST','INCLUDE_PRO_DATA_BASIS','CUSTOM_RADIUS_ATTRIBUTES','DATA_CONSUMED','PLAN_END_DATE','MAC_ADDRESS','PHONE','MOBILE','EMAIL','CREATED_AT'];
			$output = implode(",", $output);
	    	header("Content-type: text/csv");
	        header("Content-disposition: attachment; filename=\"sample_xepan_isp_user_import.csv\"");
	        header("Content-Length: " . strlen($output));
	        header("Content-Transfer-Encoding: binary");
	        print $output;
	        exit;
		}

		if($form->isSubmitted()){
			$form->js()->univ()->newWindow($form->app->url('xavoc_ispmanager_user_import',['download_sample_csv_file'=>true]))->execute();
		}

		$form_delete = $col2->add('Form');
		$form_delete->addSubmit('Delete All User Forcely')->addClass('btn btn-danger');
		if($form_delete->isSubmitted()){

			$qsp_master = $this->add('xepan\commerce\Model_QSP_Master');
			foreach ($qsp_master as $qsp) {
				$qsp->delete();
			}

			$users = $this->add('xavoc\ispmanager\Model_User');
			foreach ($users as $user) {
				$user->delete();
			}
			
			foreach ($this->add('xavoc\ispmanager\Model_UserPlanAndTopup') as $cond) {
				$cond->delete();
			}

			$this->add('xavoc\ispmanager\Model_RadCheck')->deleteAll();
			$this->add('xavoc\ispmanager\Model_RadPostAuth')->deleteAll();

			$user = $this->add('xepan\base\Model_User');
			$user->addCondition('scope','WebsiteUser');
			$user->deleteAll();

			$ci = $this->add('xepan\base\Model_Contact_Info');
			$ci->addCondition('contact_type','Customer');
			$ci->deleteAll();
			
			$this->app->db->dsql()->expr('DELETE FROM radacct;')->execute();
			$this->app->db->dsql()->expr('DELETE FROM radreply;')->execute();
			$this->app->db->dsql()->expr('DELETE FROM radusergroup;')->execute();

			$form_delete->js()->univ()->successMessage("User's Deleted Successfully")->execute();
		}

		$this->add('View')->setElement('iframe')->setAttr('src',$this->api->url('./execute',array('cut_page'=>1)))->setAttr('width','100%');
			
		$this->add('View')->setHtml('CSV Field Detail: set include_pro_data_basis value in list <b>1. none 2. invoice_only 3. data_only 4. invoice_and_data_both</b> <br/> Data_Consumed: <b>dl/ul/remark</b> in Gb, Mb <br/> Plan value = <b>plan name</b>');
	}

	function page_import_execute(){

		ini_set("memory_limit", "-1");
		set_time_limit(0);

		$form= $this->add('Form');
		$form->template->loadTemplateFromString("<form method='POST' action='".$this->api->url(null,array('cut_page'=>1))."' enctype='multipart/form-data'>
			<input type='file' name='csv_user_file'/>
			<input type='submit' value='Upload'/>
			</form>"
			);

		if($_FILES['csv_user_file']){
			if ( $_FILES["csv_user_file"]["error"] > 0 ) {
				$this->add( 'View_Error' )->set( "Error: " . $_FILES["csv_user_file"]["error"] );
			}else{
				$mimes = ['text/comma-separated-values', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.ms-excel', 'application/vnd.msexcel', 'text/anytext'];
				if(!in_array($_FILES['csv_user_file']['type'],$mimes)){
					$this->add('View_Error')->set('Only CSV Files allowed');
					return;
				}

				$importer = new \xepan\base\CSVImporter($_FILES['csv_user_file']['tmp_name'],true,',');
				$data = $importer->get();
				
				$user = $this->add('xavoc\ispmanager\Model_User');
				$user->import($data);

				// $this->add('View_Console')->set(function($c){
				// 	$c->out('-- Import Started Total Record to Import: '.count($data).'--');
				// 	$i = 1;
				// 	foreach ($data as $one_data) {
				// 		$c->out('Data: '.$i." of user ".$one_data['RADIUS_USERNAME']." -- Import started");
				// 		$c->out('Data: '.$i." of user ".$one_data['RADIUS_USERNAME']." -- Imported Successfully");

				// 		$i++;
				// 	};
				// 	$c->out('-- All Record Imported Successfully --');
				// });
				$this->add('View')->set('All Data Imported');
			}
		}

	}
}