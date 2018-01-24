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
		}
	}


	function openLead(){

		$lead = $this->add('xavoc\ispmanager\Model_Lead');
		$lead->addCondition('assign_to_id',$this->staff->id);
 		$lead->addCondition('status','Open');
 		$lead->actions = [
					'Active'=>['view','assign'],
					'Open'=>['view','assign','close','lost'],
					'Won'=>['view'],
					'Lost'=>['view','open'],
					'InActive'=>['view','activate']
				];

		$crud = $this->add('xepan\hr\CRUD',['allow_edit'=>false,'permissive_acl'=>true],null,['grid/mylead']);

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
				['name','first_name','last_name','address','city','state_id','state','country','country_id','organization','status','created_at','assign_at','emails_str','contacts_str','remark']
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
	}

	function installationLead(){

		$lead = $this->add('xavoc\ispmanager\Model_User');
		$lead->addCondition('installation_assign_to_id',$this->staff->id);
 		$lead->addCondition('status','Installation');
 		
 		$lead->actions = [
				'Won'=>['view','assign_for_installation'],
				'Installation'=>['view','payment_receive','installed'],
				'Installed'=>['view'],
				'Active'=>['view'],
				'InActive'=>['view','active']
				];

		$crud = $this->add('xepan\hr\CRUD',['allow_add'=>false,'allow_edit'=>false,'allow_del'=>false,'permissive_acl'=>true,'status_color'=>['Installation'=>'warning']],null,['grid/mylead']);
		$crud->setModel($lead);

		$crud->grid->addQuickSearch(['name','status','contacts_str','emails_str']);
		$crud->grid->addPaginator(10);
	}

}