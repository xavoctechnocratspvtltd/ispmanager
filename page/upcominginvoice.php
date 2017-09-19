<?php

namespace xavoc\ispmanager;


class page_upcominginvoice extends \xepan\base\Page {
	
	public $title ="Up-Coming Invoice";

	function init(){
		parent::init();

		$from_date = $this->app->stickyGET('from_date')?:(date('Y-m-01',strtotime($this->app->today)));
		$to_date = $this->app->stickyGET('to_date')?:$this->app->today;
		$user_name = $this->app->stickyGET('user_name');

		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
				->addContentSpot()
				->makePanelsCoppalsible()
				->layout([
						'user_name'=>'Filter~c1~3',
						'from_date'=>'c2~3',
						'to_date'=>'c3~3',
						'FormButtons~'=>'c4~3'
					]);

		$form->addField('user_name')->set($user_name);
		$form->addField('DatePicker','from_date')->set($from_date);
		$form->addField('DatePicker','to_date')->set($to_date);
		$form->addSubmit("Filter")->addClass('btn btn-primary btn-block');

		$model = $this->add('xavoc\ispmanager\Model_RecurringInvoiceItem',['from_date'=>$from_date,'to_date'=>$to_date]);
		if($user_name){
			$model->addCondition([['customer',$user_name],['radius_username',$user_name]]);
		}

		$crud = $this->add('xepan\hr\CRUD',['allow_add'=>false]);

		// filter form submission
		if($form->isSubmitted()){
			$form->js(null,$crud->js()->reload(['from_date'=>$form['from_date'],'to_date'=>$form['to_date'],'user_name'=>$form['user_name']]))->univ()->execute();
		}

		$this->customer_list = [];
		$crud->grid->addHook('formatRow',function($g){

			if(!isset($this->customer_list[$g->model['customer_id']])){
				$this->customer_list[$g->model['customer_id']] = $g->model['customer_id'];
				$g->current_row_html['customer'] = $g->model['customer']."<br/>( ".$g->model['radius_username']." )";
				$g->current_row['action'] = $g->model['action'];
			}else{
				$g->current_row['customer'] = "";
				$g->current_row_html['action'] = "";
			}
			// $other_columns = ['item'=>'<button>Create Invoice</button>','customer'=>''];
			// $g->insertBefore($other_columns);
		});

		$crud->setModel($model);
		$grid = $crud->grid;
		$grid->addColumn('customer');

		$order = $grid->addOrder();
		if($grid->hasColumn('customer'))
			$order->move('customer','first');

		$order->move('name','after','customer');
		$order->move('qty_unit','after','quantity');
		$order->move('tax_percentage','after','qty_unit');
		$order->move('new_invoice_price','after','price');
		$order->now();
		$grid->addPaginator($ipp=25);

		$removeColumn = ['qsp_master','item_template_design','rate','item_designer','item_nominal','total_amount','extra_info','customer_id','qsp_status','name','shipping_charge','shipping_duration','express_shipping_charge','express_shipping_duration','is_shipping_inclusive_tax','amount_excluding_tax','amount_excluding_tax_and_shipping','item_designer_id','item_nominal_id','item_qty_unit_id','item_qty_unit','qsp_type','sub_tax','renewable_value','renewable_unit','narration','tax_amount','taxation','is_invoice_date_first_to_first','invoice_renewable_date','include_pro_data_basis','created_at','status','attachment_icon','edit','delete','shipping_amount','radius_username'];
		foreach ($removeColumn as $key => $field_name) {
			$grid->removeColumn($field_name);
		}
	}
}