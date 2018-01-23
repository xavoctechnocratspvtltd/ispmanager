<?php

namespace xavoc\ispmanager;

class Tool_Channel_MenuBar extends \xepan\cms\View_Tool{
	
	public $options = ['login_page'=>'chanel_login'];
	
	function init(){
		parent::init();

		if($this->owner instanceof \AbstractController) return;

		$this->app->actionsWithoutACL = true;

		// checking chanel is logged in or not
		if(!$this->app->auth->isLoggedIn()){
			$this->app->redirect($this->app->url($this->options['login_page']));
			return;
		}
			
		$this->channel = $channel = $this->add('xavoc\ispmanager\Model_Channel');
		$this->channel->addCondition('user_id',$this->app->auth->model->id);
		$this->channel->tryLoadAny();

		if(!$channel->loadLoggedIn('Channel')){
			$this->add('View')->addClass('alert alert-danger')->set('you are not the permitted Channel Partner');
			return;
		}

		// end of checking chanel is logged in or not
		
		$view = $this->app->stickyGET('view');
		$page = "?page=".$this->app->page;
			
		$menu = [
				['key'=>$page."&view=dashboard",'name'=>'Dashboard'],
				['key'=>$page."&view=plan",'name'=>'Plan'],
				['key'=>$page."&view=lead",'name'=>'Lead'],
				['key'=>$page."&view=user",'name'=>'User'],
				['key'=>$page."&view=invoice",'name'=>'Invoice'],
				['key'=>$page."&view=collection",'name'=>'Payment Collection'],
				['key'=>$page."&view=settings",'name'=>'Settings'],
				['key'=>'?page=logout','name'=>'Logout']
			];
		$submenu_list = [
					'staff_lead'=>[
								'index.php?page=staff_lead&action=open'=>'Open Lead ',
								'index.php?page=staff_lead&action=installation'=>'Installation Lead '
							]
				];

		$page = $page."&view=".$view;
		// $page = $page."&view=".$view."_active";
		$this->complete_lister = $cl = $this->add('CompleteLister',null,null,['view/staffmenubar']);
		$cl->setSource($menu);
		$cl->addHook('formatRow',function($g)use($page,$submenu_list){
			$submenu_html = "";
			$submenu_class = "";

			if(isset($submenu_list[$g->model['key']])){
				$submenu_html = '<ul class="dropdown-menu">';
				foreach ($submenu_list[$g->model['key']] as $s_key => $s_value) {
					$submenu_html .= '<li><a class="dropdown-item" href="'.$s_key.'">'.$s_value.'</a></li>';
				}
				$submenu_html .= '</ul>';
				$submenu_class = "dropdown";

				$g->current_row_html['list'] = '<a href="#" class="nav-link waves-effect waves-light dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">'.$g->model['name'].' </a>';
			}else{
				$g->current_row_html['list'] = '<a class="nav-link waves-effect waves-light" href="'.$g->model['key'].'">'.$g->model['name'].'</a>';
			}

			if($g->model['key'] == $page)
				$g->current_row_html['active_menu'] = "active-nav ".$submenu_class;
			else
				$g->current_row_html['active_menu'] = "deactive-nav ".$submenu_class;
			
			$g->current_row_html['submenu'] = $submenu_html;
		});
		
		$view = $_GET['view']?:'dashboard';
		
		$this->container = $container = $this->add('View')->addClass('container');

		switch ($view) {
			case 'dashboard':
				$this->dashboard();
				break;
			case 'plan':
				$this->plan();
				break;
			case 'lead':
				$this->lead();
				break;
			case 'user':
				$this->user();
				break;
			case 'invoice':
				$this->invoice();
				break;
			case 'collection':
				$this->collection();
				break;
			case 'settings':
				$this->settings();
				break;
		}
		$this->js(true)->_selector('.dropdown-toggle')->dropdown();
	}

	function dashboard(){
		$this->container->add('view')->set('Dashboard Comming soon ...')->addClass('alert alert-success');
	}

	function plan(){
		
		$plan = $this->add('xavoc\ispmanager\Model_Channel_Plan');
		$plan->addCondition('channel_id',$this->channel->id);
		$plan->setOrder('name','asc');
		$plan->actions = [
				'Published'=>['view','edit','delete','condition'],
				'UnPublished'=>['view','edit','delete','condition']
			];

		$crud = $this->container->add('xepan\hr\CRUD',['actionsWithoutACL'=>true]);
		if($crud->isEditing()){
			$form = $crud->form;
			$form->setLayout('form/plan');
		}

		$crud->setModel($plan,
			['name','sku','description','sale_price','original_price','status','document_id','id','created_by','updated_by','created_at','updated_at','type','qty_unit_id','qty_unit','renewable_unit','renewable_value','tax_id','tax','plan_validity_value','is_auto_renew','is_renewable'],
			['name','sku','sale_price','validity','is_renewable']
		);
		$crud->grid->removeAttachment();
		$crud->grid->removeColumn('edit');
		$crud->grid->removeColumn('delete');

		$crud->grid->addQuickSearch(['name','sku']);
		$crud->grid->addPaginator(10);
	}

	function lead(){

		$this->app->actionsWithoutACL = true;
		
		$lead = $this->add('xavoc\ispmanager\Model_Channel_Lead');
		$lead->addCondition('channel_id',$this->channel->id);

		$lead->setOrder('name','asc');
		$lead->getElement('emails_str')->caption('Emails');
		$lead->getElement('contacts_str')->caption('Contacts');

 		$lead->actions = [
					// 'Active'=>['view','edit','delete','open'],
					'Open'=>['view','close','lost','edit','delete'],
					'Won'=>['view'],
					'Lost'=>['view','open'],
					'InActive'=>['view','activate','edit','delete']
				];

		$crud = $this->container->add('xepan\hr\CRUD');
		$crud->form->add('xepan\base\Controller_FLC')
			->showLables(true)
			->addContentSpot()
			->makePanelsCoppalsible(true)
			->layout([
					'first_name'=>'Lead Information~c1~4',
					'last_name'=>'c2~4',
					'organization'=>'c3~4',
					'contact_no'=>'c4~6~(,) comma seperated multiple value',
					'email_id'=>'c5~6~(,) comma seperated multiple value',
					'created_at'=>'c9~4',
					'source'=>'c10~4',
					'remark'=>'c11~4',
					'country_id~country'=>'Address~c4~2',
					'state_id~state'=>'c5~2',
					'city'=>'c6~2',
					'address'=>'c7~4',
					'pin_code'=>'c8~2'
		]);

		$crud->form->addField('line','contact_no');
		$crud->form->addField('line','email_id');

		$crud->setModel($lead,
				['first_name','last_name','address','city','state_id','country_id','organization','created_at','remark','source','pin_code'],
				['name','organization','address','city','created_at','emails_str','contacts_str','remark','type']
			);
		
		$state_field = $crud->form->getElement('state_id');
		$state_field->getModel()->addCondition('status','Active');
		if($country_id = $this->app->stickyGET('country_id')){
			$state_field->getModel()->addCondition('country_id',$country_id);
		}		
		$country_field = $crud->form->getElement('country_id');
		$country_field->getModel()->addCondition('status','Active');
		$country_field->js('change',$state_field->js()->reload(null,null,[$this->app->url(null,['cut_object'=>$state_field->name]),'country_id'=>$country_field->js()->val()]));

		$crud->grid->addHook('formatRow',function($g){
			$g->current_row_html['created_date'] = date('d M, Y',strtotime($g->model['created_at'])); 
		});

		if($crud->form->isSubmitted() && $crud->isEditing('add')){

			$emails = explode(",", $crud->form['email_id']);
			foreach ($emails as $key => $value) {
				if(!$value) continue;

				try{
					$emails = $this->add('xepan\base\Model_Contact_Email');
					$emails->addCondition('contact_id',$crud->model->id);
					$emails->addCondition('value',$value);
					$emails->tryLoadAny();
					
					$emails['head'] = "Official";
					$emails['is_active'] = true;
					$emails['is_valid'] = true;
					$emails->save();
				}catch(\Exception $e){

				}
			}

			$contacts = explode(",", $crud->form['contact_no']);
			foreach ($contacts as $key => $value) {
				if(!$value) continue;

				$phone = $this->add('xepan\base\Model_Contact_Phone');
				$phone->addCondition('contact_id',$crud->model->id);
				$phone->addCondition('value',$value);
				$phone->tryLoadAny();

				$phone['head'] = "Official";
				$phone['is_active'] = true;
				$phone['is_valid'] = true;
				$phone->save();
			}

		}

		$crud->grid->addQuickSearch(['name','status','contacts_str','emails_str']);
		$crud->grid->addPaginator(10);
		$crud->grid->removeAttachment();
		$crud->grid->removeColumn('edit');
		$crud->grid->removeColumn('delete');
		$crud->grid->removeColumn('type');
	}

	function invoice(){

		$model = $this->add('xavoc\ispmanager\Model_Channel_Invoice');
		$model->addCondition('channel_id',$this->channel->id);
		$model->setOrder('created_at','DESC');
		
		$crud = $this->container->add('xepan\hr\CRUD',
				['action_page'=>'xavoc_ispmanager_quickqsp&document_type=SalesInvoice']
				,null,
				['view/invoice/sale/grid']
			);
		$crud->setModel($model);
		$crud->grid->addPaginator(50);
		$crud->add('xepan\base\Controller_Avatar',['name_field'=>'contact']);
		
		$crud->grid->removeAttachment();
		// $crud->grid->removeColumn('delete');
	}

	function user(){

		$user = $this->add('xavoc\ispmanager\Model_Channel_User');
		$user->addCondition('channel_id',$this->channel->id);

		$crud = $this->container->add('xepan\hr\CRUD');
		$crud->setModel($user,
			['first_name','last_name','organization'],
			['name','organization','address','city','remark','created_at','emails_str','contacts_str','plan','status','type']
		);

		$crud->grid->addQuickSearch(['name','status','contacts_str','emails_str']);
		$crud->grid->addPaginator(10);
		$crud->grid->removeAttachment();
		$crud->grid->removeColumn('edit');
		$crud->grid->removeColumn('delete');
		$crud->grid->removeColumn('type');
		$crud->grid->removeColumn('status');
	}

	function collection(){
		$collection = $this->add('xavoc\ispmanager\Model_Channel_PaymentTransaction');
		$collection->addCondition('channel_id',$this->channel->id);

		$grid = $this->container->add('xepan\base\Grid');
		$grid->setModel($collection,['contact','submitted_by','payment_mode','amount','narration','created_at']);
		$grid->addHook('formatRow',function($g){
			$phtml = "";
			if($g->model['payment_mode'] == "Cash"){
				$phtml = "Payment Mode: CASH";
			}elseif($g->model['payment_mode'] == "Cheque"){
				$phtml = "Payment Mode: Cheque"."<br/>";
				$phtml .= "Cheque No: ".$g->model['cheque_no']."<br/>";
				$phtml .= "Cheque Date: ".$g->model['cheque_date']."<br/>";
				$phtml .= "Bank Detail: ".$g->model['bank_detail']."<br/>";

			}elseif($g->model['payment_mode'] == "DD"){
				$phtml = "Payment Mode: DD <br/>";
				$phtml .= "DD No: ".$g->model['dd_no']."<br/>";
				$phtml .= "dd_date: ".$g->model['dd_date']."<br/>";
				$phtml .= "Bank Detail: ".$g->model['bank_detail']."<br/>";
			}
			$g->current_row_html['payment_mode'] = $phtml;
		});

	}

	function settings(){


		$change_pass_form = $this->container->add('Form');
		$change_pass_form->add('xepan\base\Controller_FLC')
			->showLables(true)
			->makePanelsCoppalsible(true)
			->addContentSpot()
			->layout([
				'user_name'=>'Update Your Password~c1~12',
				'old_password'=>'c2~12',
				'new_password'=>'c3~12',
				'retype_password'=>'c4~12',
				'FormButtons~&nbsp'=>'c5~12'
			]);

		$change_pass_form->addField('user_name')->set($this->app->auth->model['username'])->setAttr('disabled',true);
		$change_pass_form->addField('password','old_password')->validate('required');
		$change_pass_form->addField('password','new_password')->validate('required');
		$change_pass_form->addField('password','retype_password')->validate('required');
		$change_pass_form->addSubmit('Update Password')->addClass('btn btn-success');

		if($change_pass_form->isSubmitted()){
			if( $change_pass_form['new_password'] != $change_pass_form['retype_password'])
				$change_pass_form->displayError('new_password','Password must match');
			
			if(!$this->api->auth->verifyCredentials($this->app->auth->model['username'],$change_pass_form['old_password']))
				$change_pass_form->displayError('old_password','Password not match');
			
			if($this->app->auth->model->updatePassword($change_pass_form['new_password'])){
				$this->app->auth->logout();
				$this->app->redirect($this->options['login_page']);
			}
			$change_pass_form->js()->univ()->errorMessage('some thing happen wrong')->execute();
		}
		
	}

}