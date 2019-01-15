<?php

namespace xavoc\ispmanager;

class Model_Invoice extends \xepan\commerce\Model_SalesInvoice{ 
	public $status = ['Draft','Submitted','Redesign','Due','Paid','Canceled'];
		public $actions = [
		'Draft'=>['view','cancle','edit','delete','submit','other_info','manage_attachments','communication'],
		'Submitted'=>['view','cancle','edit','delete','other_info','redesign','approve','manage_attachments','print_document','communication'],
		'Redesign'=>['view','edit','delete','submit','other_info','cancle','manage_attachments','communication'],
		'Due'=>['view','edit','delete','redesign','paid','send','cancel','other_info','manage_attachments','print_document','communication'],
		'Paid'=>['view','edit','delete','send','cancel','other_info','manage_attachments','print_document','communication'],
		'Canceled'=>['view','edit','delete','paid','redraft','other_info','manage_attachments','communication']
		];

	// function page_recurring_invoice($page){
	// 	$page->add('View')->set("recurring invoice");

	// }

	function init(){
		parent::init();

		$this->addExpression('invoice_number')->set(function($m,$q){
			return $q->expr('concat(IFNULL([0],"")," ",IFNULL([1],"")," :: INR(",IFNULL([2],0),") :: ",IFNULL(date([3]),""), " :: ",[4])',
				[
					$this->getElement('serial'),
					$this->getElement('document_no'),
					$this->getElement('net_amount'),
					$this->getElement('created_at'),
					$this->getElement('status')
				]);
		});
	}

	function page_approve($page){

		$customer = $this->add('xavoc\ispmanager\Model_User');
		$customer->addCondition('id',$this['contact_id']);
		$customer->tryLoadAny();
		if(!$customer->loaded()) throw new \Exception("customer not found");
		
		$user = $this->add('xavoc\ispmanager\Model_User');
		$user->loadBy('radius_username',$customer['radius_username']);
		
		$items = $this->Items()->getRows();
		$items_ids = array_column($items, 'item_id');
		$plan = $this->add('xavoc\ispmanager\Model_Plan')
					->addCondition('is_topup',false)
					->addCondition('id',$items_ids)->tryLoadAny();

		if($plan->loaded()){
			$oi = $this->Items()->tryLoadBy('item_id',$plan->id);
			$cm = $user->getLastCondition();
			if(!$cm->loaded()) throw new \Exception("Plan Not Implemented On User ".$this['radius_username']." do it manually");
			$on_date = $cm['end_date'];

			$info = "User: ".$user['effective_name']." [".$user['radius_username']."]"."<br/>";
			$page->add('View')->setHtml($info);

			$col = $page->add('Columns');
			$col1 = $col->addColumn(5);
			$col2 = $col->addColumn(2);
			$col3 = $col->addColumn(5);

			$info = "<div class='alert alert-info'>Current Plan Condition</div>".
					"Plan: ".$cm['plan']."<br/>".
					"<div class='label label-info'>End Date: ".$cm['end_date']."</div></br>".
					"<div class='label label-danger'>Expire Date: ".$cm['expire_date']."</div></br>"
					;
			$col1->add('View')->setHtml($info);

			$next_end_date = date("Y-m-d H:i:s", strtotime("+".$plan['plan_validity_value']." ".$plan['qty_unit'],strtotime($cm['end_date'])));
			$next_expire_date = date("Y-m-d H:i:s", strtotime("+".($user['grace_period_in_days']?:0)." days",strtotime($next_end_date)));
			$info = "<div class='alert alert-warning'>After Invoice Approved Condition will be</div>".
					"Plan: ".$plan['plan_name_with_code']."<br/>".
					"<div class='label label-danger'>End Date: ".$next_end_date."</div></br>".
					"<div class='label label-success'>Expire Date: ".$next_expire_date."</div></br>"
					;
			$col3->add('View')->setHtml($info);
			
			$col = $page->add('Columns');
			$col2 = $col->addColumn(12)->addClass('text-center');
			$form = $col2->add('Form');
			$form->addField('checkbox','update_condition')->set(true);
			$form->addSubmit('approve invoice now')->addClass('btn btn-primary');

			if($form->isSubmitted()){
				$this->app->isp_invoice_approved_function_not_run = !($form['update_condition']?1:0);
				$this->approve();

				$this->app->page_action_result = $form->js()->univ()->closeDialog();
			}
		}else{
			$form = $page->add('Form')->addClass('text-center');
			$form->addSubmit('approve invoice now')->addClass('btn btn-primary');
			if($form->isSubmitted()){
				$this->app->isp_invoice_approved_function_not_run = 1;
				$this->approve();
				$this->app->page_action_result = $form->js()->univ()->closeDialog();
			}
		}	

			

	} 

}