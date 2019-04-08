<?php

namespace xavoc\ispmanager;

class page_listuser extends \xepan\base\Page{
	public $title = "User List";
	public $datastatus = false;
	public $model_class = "xavoc\ispmanager\Model_User";
	public $paginator = 10;

	function init(){
		parent::init();

		$this->addFilterForm();

		$this->setModel();
		$this->crud = $crud = $this->add('xepan\hr\CRUD',['entity_name'=>'User']);
		$this->app->stickyGET('status');

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
						'branch_id~Branch'=>'c8~12',
						
						'plan~Plan'=>'User Plan Information~c1~4',
						'radius_username~Radius User Name'=>'c2~4',
						'radius_password'=>'c3~4',
						'simultaneous_use'=>'c4~4',
						'grace_period_in_days'=>'c5~4',
						'mac_address'=>'c6~4',
						// 'custom_radius_attributes'=>'c7~12',
						'create_invoice~'=>'c8~12',
						'is_invoice_date_first_to_first~'=>'c9~12',
						'include_pro_data_basis'=>'c10~12',
					]);
			// $form->setLayout('form/user');
		}

		$this->crud->grid->addHook('formatRow',function($g){
				$acc = $this->add('xavoc\ispmanager\Model_RadAcct');
				$acc->addCondition('username',$g->getmodel()['radius_username']);
				$acc->addCondition('acctstoptime',null);
				$acc->setOrder('radacctid','desc');
				$acc->tryLoadAny();
				$framed_ip_address = $acc['framedipaddress'];

				$key = 'checkAuthenticationReadOnly(null,"'.$g->getModel()['radius_username'].'")';
				$query = 'select '.$key;
				$data_status = $this->app->db->dsql()->expr($query)->execute()->get();
				$radius_login_response = $data_status[0][$key];

				$data = explode(",",$radius_login_response);
				$limits = explode("/", $data[2]);
				$ul_limit = $this->app->byte2human($limits[0]);
				// $g->current_row_html['datastatus'] = ;
				$dl_limit = $this->app->byte2human($limits[1]);
				$data[2] = $ul_limit.' / '.$dl_limit;

				// data status
				// find prosiible cause of no access 
				$access = "Yes";
				if(!$data[0]){
					$access = "No Valid Plan Condition Matched: Authentication failed";
					if($g->model['is_expired'] || strtotime($this->app->now) > strtotime($g->model['active_plan_end_date'])) $access=" Plan Expired / Ended : Authentication Failed ";
				}
				$g->current_row_html['datastatus'] = 'Access: '.($data[0]?'<span class="label label-success">yes</span>':'<br/><span class="label label-danger" style="font-size:8px;">'.$access.'</span>').'<br/>'.'COA: '.($data[1]?'yes':'no').'<br/>UL / DL: '.$data[2].'<br/>Burst: '.$data['3']."<br/>Expire Date: ".($g->model['active_plan_expire_date']?date('d-M-Y',strtotime($g->model['active_plan_expire_date'])):"");

				// add online /offline
				$status = ($g->model['is_online'] && $data[0]) ? "Online":"Offline";
				$g->current_row_html['radius_username'] = $g->model['radius_username']."<br>".$g->model['name']."<br/><div class='".$status."'><i class='fa fa-circle'></i>&nbsp;".$status."</div>IP: ".$framed_ip_address."<br/>Branch:".$g->model['branch'];


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
		$this->crud->grid->addColumn('datastatus');
		// $crud->grid->addFormatter('contacts_str','Wrap');
		$crud->grid->addFormatter('datastatus','Wrap');

		if($s = $_GET['status']){
			$this->model->addCondition('new_status',$s);
		}else{
			$this->model->addCondition('new_status',['Active','InActive','Expired']);
		}

		$this->crud->setModel($this->model,['net_data_limit','branch_id','radius_username','radius_password','plan_id','simultaneous_use','grace_period_in_days','custom_radius_attributes','first_name','last_name','create_invoice','is_invoice_date_first_to_first','include_pro_data_basis','country_id','state_id','city','address','pin_code','qty_unit_id','mac_address'],['name','radius_username','plan','radius_login_response','contacts_str','emails_str','created_at','last_login','is_online','active_condition_data','last_logout','name','created_by','active_plan_expire_date','due_date','branch','address','city','state','country']);
		$this->crud->grid->addPaginator(10);
		$crud->grid->addSno();
		$crud->grid->add('misc\Export');

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

		$o = $crud->grid->addOrder();
		$o->move('datastatus','after','plan');
		$o->now();

		$this->crud->grid->removeColumn('attachment_icon');
		$this->crud->grid->removeColumn('branch');
		$this->crud->grid->removeColumn('address');
		$this->crud->grid->removeColumn('city');
		$this->crud->grid->removeColumn('state');
		$this->crud->grid->removeColumn('country');
		$this->crud->grid->removeColumn('last_logout');
		$crud->grid->removeColumn('emails_str');
		$crud->grid->removeColumn('name');
		$crud->grid->removeColumn('active_condition_data');
		$crud->grid->removeColumn('is_online');
		$crud->grid->removeColumn('created_by');
		$crud->grid->removeColumn('active_plan_expire_date');

		$filter_form = $crud->grid->addQuickSearch(['name','radius_username','plan','contacts_str','emails_str']);
		$crud->grid->addSno();
		$this->addTopBarStatusFilter();

		$this->formSubmit();
	}

	function setModel(){
		$filter = $this->app->stickyGET('filter');
		$from_date = $this->app->stickyGET('from_date');
		$to_date = $this->app->stickyGET('to_date');
		$look_according_to = $this->app->stickyGET('look_according_to');
		$user = $this->app->stickyGET('user');
		$employee = $this->app->stickyGET('employee');
		$city = $this->app->stickyGET('city');
		$connection_status = $this->app->stickyGET('connection_status');

		$this->model = $model = $this->add('xavoc\ispmanager\Model_User');
		$model->getElement('country_id')->getModel('status','Active');
		$model->getElement('state_id')->getModel('status','Active');

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
			// $upt->addCondition('plan_id',$m->getElement('plan_id'));
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

		$model->addExpression('is_online')->set(function($m,$q){
			$t = $m->add('xavoc\ispmanager\Model_RadAcct')
						->addCondition('username',$m->getElement('radius_username'))
						->setOrder('radacctid','desc')
						->setLimit(1);
			return $q->expr('IF([0] is not null AND [1] is null ,1,0)',[$t->fieldQuery('radacctid'),$t->fieldQuery('acctstoptime')]);
		})->sortable(true)->type('boolean');

		$model->addExpression('new_status')->set(function($m,$q){
			return $q->expr('if(([0]=1 AND [1]="Active"),"Expired",[1])',[$m->getElement('is_expired'),$m->getElement("status")]);
		});

		$model->addCondition('status',['InActive','Active']);

		if(@$this->app->branch->id AND $model->hasElement('branch_id')){
			$model->addCondition('branch_id',$this->app->branch->id);
		}

		if($filter){
			if($user){
				$model->addCondition('id',$user);

			}else{

				if($from_date && $look_according_to) $model->addCondition($look_according_to,">=",$from_date);
				if($to_date && $look_according_to) $model->addCondition($look_according_to,"<", $this->app->nextDate($to_date?$to_date:$this->app->today));

				if($employee) $model->addCondition('created_by_id',$employee);
				if($city) $model->addCondition('city',$city);
				if($connection_status){
					if($connection_status == "Online"){
						$model->addCondition('is_online',true);
					}
					if($connection_status == "Offline"){
						$model->addCondition('is_online',false);
					}
				}

			}

		}

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

		// $model->getElement('emails_str')->caption('Emails');
		// $model->getElement('contacts_str')->caption('Contacts');
		$model->setOrder('id','desc');

		// $model->add('xepan\base\Controller_TopBarStatusFilter',['add_status_to_sidebar'=>['Active','InActive','InDemo']]);
	}

	function addTopBarStatusFilter(){
		$user_model = $this->add('xavoc\ispmanager\Model_User');
		$user_model->status = ['Active','InActive','Expired'];
		$user_model->status_color = ['Active'=>'primary','InActive'=>'danger','Expired'=>'warning'];

		$user_model->addExpression('is_expired')->set(function($m,$q){
			$upt = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
			// $upt->addCondition('plan_id',$m->getElement('plan_id'));
			$upt->addCondition('user_id',$m->getElement('id'));
			$upt->addCondition('is_effective',true);
			$upt->setOrder('id','desc');
			$upt->setLimit(1);
			return $q->expr('IFNULL([0],0)',[$upt->fieldQuery('is_expired')]);
		});
		$user_model->addExpression('new_status')->set(function($m,$q){
			return $q->expr('if(([0]=1 AND [1]="Active"),"Expired",[1])',[$m->getElement('is_expired'),$m->getElement("status")]);
		});
		$user_model->addExpression('total_record')->set('count(*)');
		$user_model->add('xepan\hr\Controller_ACL');
		if(@$this->app->branch->id AND $user_model->hasElement('branch_id')){
			$user_model->addCondition('branch_id',$this->app->branch->id);
		}
		$user_model->addCondition('status',['Active','InActive']);
		$user_model->_dsql()->group('new_status');

		$total_count = 0;
		foreach ($user_model as $m) {
			if(!in_array($m['new_status'], $m->status) ) continue;

			$total_count += $m['total_record'];

			$this->app->page_top_right_button_set->addButton([$m['new_status']." (".$m['total_record'].")",'icon'=>'primary'])
					->addClass('btn btn-'.$m->status_color[$m['new_status']])
					->js('click')->univ()->location($this->api->url(null,['status'=>$m['new_status']]))
				;

			if($_GET['status'] == $m['new_status'])
				$this->title .= ' ['.$m['new_status'] .' :'. $m['total_record'] .']';
		}

		$this->app->page_top_right_button_set->addButton(['All'."(".$total_count.")",'icon'=>'primary'])
					->addClass('btn btn-primary')
					->js('click')->univ()->location($this->api->url(null,['status'=>null]))
				;

	}

	function addFilterForm(){
		$this->filter_form = $form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->showLables(true)
			->makePanelsCoppalsible(true)
			->layout([
					'from_date'=>'Filter~c1~4~closed',
					'to_date'=>'c2~4',
					'look_according_to'=>'c3~4',
					'user~Radius User'=>'c4~3',
					'employee~User Created By Employee'=>'c5~3',
					'city'=>'c6~3',
					'connection_status'=>'c7~3',

					'FormButtons~&nbsp;'=>'z1~3'
				]);
		$form->addField('DatePicker','from_date');
		$form->addField('DatePicker','to_date');

		$user_model = $this->add('xavoc\ispmanager\Model_User');
		$user_model->title_field = "radius_effective_name";
		$form->addField('autocomplete\Basic','user')->setModel($user_model);
		$look_field = $form->addField('DropDown','look_according_to')
				->setValueList(
					[
					'radius_user_created_at'=>'Created Date',
					'last_login'=>'Last Login',
					'last_logout'=>'Last Logout',
					'due_date'=>'Due Date',

				]);
		$look_field->setEmptyText('select date variable');

		$form->addField('autocomplete\Basic','employee')->setModel('xepan\hr\Model_Employee');

		$data = $this->app->db->dsql()->expr('SELECT DISTINCT(city) AS city FROM contact')->get();
		$city_list = [];
		foreach ($data as $key => $value) {
			if(!trim($value['city'])) continue;
			$city_list[$value['city']] = $value['city'];
		}
		$city_field = $form->addField('DropDown','city');
		$city_field->setValueList($city_list);
		$city_field->setEmptyText('Select City');

		$connection_status_field = $form->addField('DropDown','connection_status');
		$connection_status_field->setValueList(['Online'=>'Online','Offline'=>'Offline']);
		$connection_status_field->setEmptyText('Both Online/Offline');

		$this->filter_btn = $form->addSubmit('Apply Filter')->addClass('btn btn-primary');
		$this->clear_btn = $form->addSubmit('clear')->addClass('btn btn-warning');

	}


	function formSubmit(){
		if($this->filter_form->isSubmitted()){
			$form_data = [
					'filter'=>0
				];

			if($this->filter_form->isClicked($this->clear_btn)){
				$this->crud->js(null,$this->filter_form->js()->reload())->reload($form_data)->execute();
			}			

			$form_data = $this->filter_form->getAllFields();

			if(($form_data['from_date'] OR $form_data['to_date']) AND ! $form_data['look_according_to']) $this->filter_form->displayError('look_according_to','look according to must not be empty');

			$form_data['filter'] = 1;
			$this->crud->js()->reload($form_data)->execute();
		}
	}

}