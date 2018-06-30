<?php

namespace xavoc\ispmanager;

/**
* 
*/
class Tool_Staff_MyLead extends \xepan\cms\View_Tool{
	public $options = [];
	
	
	function init(){
		parent::init();
		
		if(!$this->app->auth->isLoggedIn()){
			$this->app->redirect($this->app->url('staff_login'));
			return;
		}
		$action = $this->app->stickyGET('action')?:"open";

		// $staff = $this->add('xepan\base\Model_Contact');
		// $staff->loadLoggedIn();
		$this->staff = $this->add('xepan\hr\Model_Employee');
		$this->staff->loadLoggedIn();

		if(!$this->staff->loaded())
			throw new \Exception("staff not found");
		
		switch ($action) {
			case 'open':
				$this->openLead();
				break;
			case 'installation':
				$this->installationLead();
				break;
			case 'leads':
				$this->allLead();
				break;
			case 'paymentcollection':
				$this->paymentCollection();
				break;
			case 'managestock':
				$this->manageStock();
				break;
		}
	}


	function openLead(){

		$lead = $this->add('xavoc\ispmanager\Model_Lead');
		$lead->addCondition('assign_to_id',$this->staff->id);

 		$lead->addCondition('status','Open');
 		$lead->actions = [
					'Active'=>['view','assign','append_remark'],
					'Open'=>['view','assign','won','lost','append_remark'],
					'Won'=>['view','append_remark'],
					'Lost'=>['view','open','append_remark'],
					'InActive'=>['view','activate','append_remark']
				];

		$crud = $this->add('xepan\hr\CRUD',['allow_edit'=>false,'permissive_acl'=>true]);

		$crud->form->add('xepan\base\Controller_FLC')
			->showLables(true)
			->addContentSpot()
			->makePanelsCoppalsible(true)
			->layout([
					'first_name'=>'Lead Information~c1~4',
					'last_name'=>'c2~4',
					'organization'=>'c3~4',
					'contact_no'=>'c4~6',
					'email_id'=>'c5~6',
					'created_at'=>'c9~4',
					'source'=>'c10~4',
					'remark'=>'c11~4',
					'country_id~country'=>'Address~c4~2',
					'state_id~state'=>'c5~2',
					'city'=>'c6~2',
					'address'=>'c7~4',
					'pin_code'=>'c8~2'
				]);

		$crud->form->addField('Number','contact_no')->validate('to_trim|required');
		$crud->form->addField('line','email_id')->validate('email');

		$crud->setModel($lead,
				['first_name','last_name','address','city','state_id','country_id','organization','created_at','remark','source','pin_code'],
				['name','address','city','state','organization','status','created_at','assign_at','emails_str','contacts_str','remark','created_by','assign_to']
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
			$g->current_row_html['address'] = $g->model['address']."<br/>".$g->model['city']."<br/>".$g->model['state'];
			$g->current_row_html['name'] = $g->model['name']."<br/>( ".$g->model['organization']." )";
			$g->current_row_html['created_at'] = $g->model['created_at']."<br/>( ".$g->model['created_by']." )";
			$g->current_row_html['assign_at'] = $g->model['assign_at']."<br/>( ".$g->model['assign_to']." )";

		});

		if($crud->form->isSubmitted() && $crud->isEditing('add')){

			$emails = $this->add('xepan\base\Model_Contact_Email');
			$emails['contact_id'] = $crud->model->id;
			$emails['head'] = "Official";
			$emails['value'] = $crud->form['email_id'];
			$emails['is_active'] = true;
			$emails['is_valid'] = true;
			$emails->save();

			$phone = $this->add('xepan\base\Model_Contact_Phone');
			$phone['contact_id'] = $crud->model->id;
			$phone['head'] = "Official";
			$phone['value'] = $crud->form['contact_no'];
			$phone['is_active'] = true;
			$phone['is_valid'] = true;
			$phone->save();
		}

		$crud->grid->addQuickSearch(['name','status','contacts_str','emails_str']);
		$crud->grid->addPaginator(10);
		$crud->grid->addSno();

		$crud->grid->removeColumn('status');
		$crud->grid->removeColumn('state');
		$crud->grid->removeColumn('city');
		$crud->grid->removeColumn('organization');
		$crud->grid->removeColumn('created_by');
		$crud->grid->removeColumn('assign_to');
		$crud->grid->removeAttachment();

	}

	function installationLead(){

		$lead = $this->add('xavoc\ispmanager\Model_User');
		$lead->addCondition('installation_assign_to_id',$this->staff->id);
 		$lead->addCondition('status','Installation');
 		
 		$lead->actions = [
				'Won'=>['view','assign_for_installation'],
				'Installation'=>['view','installed','lost'],
				'Installed'=>['view'],
				'Active'=>['view'],
				'InActive'=>['view','active']
				];

		$crud = $this->add('xepan\hr\CRUD',['allow_add'=>false,'allow_edit'=>false,'allow_del'=>false,'permissive_acl'=>true,'status_color'=>['Installation'=>'warning']]);
		$crud->setModel($lead,['name','address','city','state','organization','created_at','installation_assign_at','installation_assign_to','emails_str','contacts_str','remark','installed_narration','created_by','status']);

		$grid = $crud->grid;
		$grid->addHook('formatRow',function($g){
			$g->current_row_html['address'] = $g->model['address']."<br/>".$g->model['city']."<br/>".$g->model['state'];
			$g->current_row_html['name'] = $g->model['name']."<br/>( ".$g->model['organization']." )";
			$g->current_row_html['created_at'] = $g->model['created_at']."<br/>( ".$g->model['created_by']." )";
			$g->current_row_html['installation_assign_at'] = $g->model['installation_assign_at']."<br/>( ".$g->model['installation_assign_to']." )";
			$g->current_row_html['remark'] = $g->model['remark']."<br/>".$g->model['installed_narration'];

		});
		$grid->removeColumn('state');
		$grid->removeColumn('status');
		$grid->removeColumn('city');
		$grid->removeColumn('created_by');
		$grid->removeColumn('installation_assign_to');
		$grid->removeColumn('installed_narration');
		$grid->removeColumn('organization');
		$grid->removeAttachment();

		$grid->addQuickSearch(['name','contacts_str','emails_str','address','city']);
		$grid->addPaginator(25);
	}

	function alllead(){

		$lead = $this->add('xavoc\ispmanager\Model_Lead');
		$lead->addCondition([['created_by_id',$this->staff->id],['assign_to_id',$this->staff->id]]);
		$lead->setOrder('created_at','desc');
		$grid = $this->add('xepan\hr\Grid');
		$grid->setModel($lead,['name','address','city','state','organization','created_at','assign_at','emails_str','contacts_str','remark','created_by','assign_to','status']);
		
		$grid->addHook('formatRow',function($g){
			$g->current_row_html['address'] = $g->model['address']."<br/>".$g->model['city']."<br/>".$g->model['state'];
			$g->current_row_html['name'] = $g->model['name']."<br/>( ".$g->model['organization']." )";
			$g->current_row_html['created_at'] = $g->model['created_at']."<br/>( ".$g->model['created_by']." )";
			$g->current_row_html['assign_at'] = $g->model['assign_at']."<br/>( ".$g->model['assign_to']." )";

			$g->current_row_html['status'] = '<div class="alert alert-info">'.$g->model['status'].'</div>';
		});
		$grid->removeColumn('state');
		$grid->removeColumn('city');
		$grid->removeColumn('created_by');
		$grid->removeColumn('assign_to');
		$grid->removeColumn('organization');
		$grid->removeAttachment();

		$grid->addQuickSearch(['name','contacts_str','emails_str','address','city']);
		$grid->addPaginator(25);
	}

	function paymentCollection(){

		$payment_tra = $this->add('xavoc\ispmanager\Model_PaymentTransaction');
		$payment_tra->getElement('payment_mode')->caption('Payment Detail');
		$payment_tra->addCondition('employee_id',$this->staff->id);
		$payment_tra->setOrder('id','desc');

		$crud = $this->add('xepan\hr\CRUD',['pass_acl'=>true,'allow_edit'=>false,'allow_del'=>false]);
		$crud->setModel($payment_tra,['employee','contact_id','created_at','amount','payment_mode','narration'],['contact','created_at','amount','payment_mode','narration','is_submitted_to_company']);

		$crud->grid->addHook('formatRow',function($g){
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

		$crud->grid->addPaginator(25);
		$filter_form = $crud->grid->addQuickSearch(['contact','amount']);

		$field = $filter_form->addField('DropDown','is_submitted');
		$field->setValueList(['is_submitted'=>'Submitted to Company','not_submitted'=>'Not Submitted']);
		$field->setEmptyText('All Record');

		$filter_form->addHook('applyFilter',function($f,$m){
			if($f['is_submitted'] == "is_submitted"){
				$m->addCondition('is_submitted_to_company',true);
			}
		});


	}

	function manageStock(){
		$tabs = $this->add('Tabs');
		$stock_tab = $tabs->addtab('My Stocks');
		$to_receive_tab = $tabs->addtab('My To Receive');
		$receive_tab = $tabs->addtab('Received');

		$stock_model=$this->add('xepan\commerce\Model_Item_Stock',['warehouse_id'=>$this->staff->id]);
		$stock_model->addCondition('maintain_inventory',true);
		$grid= $stock_tab->add('xepan\base\Grid',['fixed_header'=>false]);
		$grid->setModel($stock_model,['name','net_stock','qty_unit']);
		$grid->addPaginator(10);
		
		$to_rec_model = $to_receive_tab->add('xepan\commerce\Model_Store_Transaction');
		$to_rec_model->actions=[
				'ToReceived'=>['view','receive']
			];
		$to_rec_model->addCondition('to_warehouse_id',$this->staff->id);
		$to_rec_model->addCondition('status','ToReceived');
		$to_rec_model->addCondition('item_quantity','>',0);

		$grid =$to_receive_tab->add('xepan\hr\CRUD',['allow_add'=>false, 'allow_del'=>false ,'allow_edit'=>false,'actionsWithoutACL'=>true,['grid_options'=>['fixed_header'=>false]]]);
		$grid->setModel($to_rec_model,['from_warehouse','created_by','type','status','item_quantity','toreceived','received']);
		$grid->removeAttachment();

		$to_rec_model = $receive_tab->add('xepan\commerce\Model_Store_TransactionRow');
		$to_rec_model->addCondition('to_warehouse_id',$this->app->employee->id);
		$to_rec_model->addCondition('status','Received');
		
		$grid = $receive_tab->add('xepan\hr\CRUD',['allow_add'=>false, 'allow_del'=>false ,'allow_edit'=>false,'actionsWithoutACL'=>true,['grid_options'=>['fixed_header'=>false]]]);
		$grid->setModel($to_rec_model,['item_name','quantity','extra_info','serial_nos','narration','from_warehouse','created_at']);
		$grid->removeAttachment();
	}
}