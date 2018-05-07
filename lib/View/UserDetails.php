<?php

namespace xavoc\ispmanager;

class View_UserDetails extends \View {
	
	public $user=null;
	public $allow_edit=false;

	function init(){
		parent::init();

		if(!$this->user){
			$this->add('View')->addClass('alert alert-danger')->set('Please set User');
			return;
		}

		if($this->allow_edit) 
			$this->manageEdit();
		else
			$this->manageView();
	}

	function manageEdit(){
		$this->add('H3')->set($this->user['name_with_type']);
		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
		->showLables(true)
		->makePanelsCoppalsible(true)
		->layout([
				'first_name'=>'User Details~c1~6~closed',
				'last_name'=>'c2~6',
				'organization'=>'c3~12',
				'city'=>'c4~4', // closed to make panel default collapsed
				'address'=>'c5~4',
				'pin_code'=>'c6~4',
				'current_plan~'=>'Current Plan~c7~12',
				'invoices~'=>'Invoices~c7~12',
				'plan_history~'=>'Plan History~c7~12~closed'
			]);

		$form->setModel($this->user,['first_name','last_name','organization','city','address','pin_code']);

		$current_plan = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
		$current_plan->addCondition('user_id',$this->user->id);
		$current_plan->addCondition('is_expired',false);

		$c=$form->layout->add('xepan\hr\CRUD',null,'current_plan');
		$c->setModel($current_plan,['plan','start_date','end_date','expire_date','reset_date'],['plan','start_date','end_date','expire_date','reset_date']);
		$c->noAttachment();
		$c->grid->removeColumn('action');

		$plan_history = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
		$plan_history->addCondition('user_id',$this->user->id);
		$plan_history->addCondition('is_expired',true);

		$c=$form->layout->add('xepan\hr\CRUD',null,'plan_history');
		$c->setModel($plan_history,['plan','start_date','end_date','expire_date','reset_date'],['plan','start_date','end_date','expire_date','reset_date']);
		$c->noAttachment();
		$c->grid->removeColumn('action');

		$invoices = $this->add('xavoc\ispmanager\Model_Invoice');
		$invoices->addCondition('contact_id',$this->user->id);

		$c=$form->layout->add('xepan\hr\CRUD',['allow_add'=>false,'allow_edit'=>false,'allow_del'=>false],'invoices');
		$c->setModel($invoices,['created_at','serial','document_no','total_amount','net_amount','status']);
		$c->noAttachment();
		$c->grid->removeColumn('action');
		$dc = $c->addRef('Details',['view_class'=>'Grid','fields'=>['item','price','qty_unit','amount_excluding_tax','taxation','tax_amount','total_amount']]);

		if($form->isSubmitted()){
			$form->save();
			$form->js(null,$form->js()->univ()->successMessage('Information Updated'))->univ()->closeDialog()->execute();
		}

	}

}