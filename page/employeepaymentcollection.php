<?php

namespace xavoc\ispmanager;


class page_employeepaymentcollection extends \xepan\base\Page {
	
	public $title ="Employee Payment Collection";

	function init(){
		parent::init();

		$customer_id = $this->app->stickyGET('selectcustomerid');
		$f_customer = $this->app->stickyGET('filter_customer');
		$f_employee = $this->app->stickyGET('filter_employee');
		$f_from_date = $this->app->stickyGET('filter_from_date');
		$f_to_date = $this->app->stickyGET('filter_to_date');

		$filter_form = $this->add('Form');
		$filter_form->add('xepan\base\Controller_FLC')
			->makePanelsCoppalsible(true)
			->layout([
				'date_range'=>'Filter~c1~2~closed',
				'collection_by_employee'=>'c2~3',
				'customer'=>'c3~3',
				'FormButtons~&nbsp;'=>'c4~2'
			]);

		$date_field = $filter_form->addField('DateRangePicker','date_range');
		$set_date = $this->app->today." to ".$this->app->today;
		$date_field->set($set_date);

		$filter_form->addField('Dropdown','collection_by_employee')
			->setEmptyText('Please Select Employee..')
			->setModel('xepan\hr\Model_Employee')
			->addCondition('status','Active');

		$cust_field = $filter_form->addField('xepan\base\Basic','customer');
		$cst_model = $this->add('xavoc\ispmanager\Model_User');
		$cust_field->setModel($cst_model);
		$filter_form->addSubmit('Filter');

		$payment_tra = $this->add('xavoc\ispmanager\Model_PaymentTransaction');
		$payment_tra->getElement('payment_mode')->caption('Payment Detail');
		$payment_tra->setOrder('id','desc');
		$payment_tra->getElement('submitted_by')->caption('Submission Detail');

		if($f_from_date)
			$payment_tra->addCondition('created_at','>=',$f_from_date);
		if($f_to_date)
			$payment_tra->addCondition('created_at','<',$this->app->nextDate($f_to_date));
		if($f_employee)
			$payment_tra->addCondition('employee_id',$f_employee);
		if($f_customer)
			$payment_tra->addCondition('contact_id',$f_customer);


		$crud = $this->add('xepan\hr\CRUD');
		$form = $crud->form;
		$form->add('xepan\base\Controller_FLC')
		->showLables(true)
		->addContentSpot()
		->makePanelsCoppalsible(true)
		->layout([
				'contact~Customer'=>'Payment Collection Detail~c1~6',
				// 'contact'=>'c1~6',
				'invoice_id~Invoice Number'=>'c2~6',
				'payment_mode'=>'c3~6',
				'amount'=>'c4~6',
				'cheque_no'=>'c5~6',
				'cheque_date'=>'c6~6',
				'dd_no~Other Payment Receipt/Transaction No'=>'c7~6',
				'dd_date~Payment / Transaction on Date'=>'c8~6',
				'bank_detail'=>'c41~6',
				'narration'=>'c42~6'
			]);

		$crud->setModel($payment_tra,
			['employee','contact_id','contact','invoice_id','created_at','payment_mode','amount','cheque_no','cheque_date','dd_no','dd_date','bank_detail','narration','submitted_by','submitted_at','status'],
			['employee','contact_id','contact','invoice_number','created_at','payment_mode','amount','cheque_no','cheque_date','dd_no','dd_date','bank_detail','narration','is_submitted_to_company','submitted_by','submitted_at','status']);

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
				$phtml = "Payment Mode: Other/ Online <br/>";
				$phtml .= "Receipt/Transaction No: ".$g->model['dd_no']."<br/>";
				$phtml .= "Payment / Transaction on: ".$g->model['dd_date']."<br/>";
				$phtml .= "Bank Detail: ".$g->model['bank_detail']."<br/>";
			}

			$g->current_row_html['payment_mode'] = $phtml;

			if($g->model['is_submitted_to_company'])
				$g->current_row_html['submitted_by'] = "Yes,<br/>Submitted to company on ".$g->model['submitted_at']."<br/> Received by: ".$g->model['submitted_by'];
			else
				$g->current_row_html['submitted_by'] = "";
		});

		$crud->grid->addFormatter('invoice_number','Wrap');

		if($crud->isEditing()){
			$form = $crud->form;
			$payment_mode_field = $form->getElement('payment_mode');

			$payment_mode_field->js(true)->univ()->bindConditionalShow([
				'Cash'=>['amount','narration'],
				'Cheque'=>['cheque_no','cheque_date','bank_detail','amount','narration'],
				'DD'=>['dd_no','dd_date','bank_detail','amount','narration'],
			],'.flc-atk-form-row');

			$customer_field = $form->getElement('contact_id');
			$inv_field = $form->getElement('invoice_id');
			if($customer_id){
				$inv_field->getModel()
						->addCondition('contact_id',$customer_id)
						->addCondition('status','<>',"Paid")
						;
			}else{
				$inv_field->getModel()->addCondition('id','-1');
			}

			// $reload_field_array = [
			// 				$form->js()->atk4_form(
			// 					'reloadField','invoice_id',[
			// 							$this->app->url(),
			// 							'selected_customer_id'=>$customer_field->js()->val()
			// 						]
			// 					)
			// 				];
			// $this->country_field->js('change',);
			// $customer_field->js('change',[$inv_field->js(null,[$inv_field->js()->select2('destroy')])->reload(null,null,[$this->app->url(null,['cut_object'=>$inv_field->name]),'select_customer_id'=>$customer_field->js()->val()])]);
			$customer_field->other_field->js('change',[$inv_field->js(null,[$inv_field->js()->select2('destroy')])->reload(null,null,[$this->app->url(null,['cut_object'=>$inv_field->name]),'selectcustomerid'=>$customer_field->js()->val()])]);
		}

		$crud->grid->removeAttachment();
		$crud->grid->removeColumn('edit');
		$crud->grid->removeColumn('status');
		$crud->grid->removeColumn('is_submitted_to_company');
		$crud->grid->removeColumn('submitted_at');
		$crud->grid->removeColumn('cheque_no');
		$crud->grid->removeColumn('cheque_date');
		$crud->grid->removeColumn('dd_no');
		$crud->grid->removeColumn('dd_date');
		$crud->grid->removeColumn('bank_detail');
		$crud->grid->removeColumn('delete');
		$crud->grid->removeColumn('contact_id');

		$crud->grid->addPaginator($ipp=50);
		$crud->grid->addFormatter('contact','wrap');
		$crud->grid->addFormatter('submitted_by','wrap');
		$crud->grid->addTotals(['amount']);

		if($filter_form->isSubmitted()){

			$crud->js()->reload(
					[
						'filter_employee'=>$filter_form['collection_by_employee'],
						'filter_from_date'=>$date_field->getStartDate()?:0,
						'filter_to_date'=>$date_field->getEndDate()?:0,
						'filter_customer'=>$filter_form['customer']
					]
			)->execute();
		}

	}
}