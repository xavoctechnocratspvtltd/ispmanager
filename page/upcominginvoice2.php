<?php

namespace xavoc\ispmanager;


class page_upcominginvoice2 extends \xepan\base\Page {
	
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
						'user_name'=>'Filter~c1~4',
						'from_date'=>'c2~2',
						'to_date'=>'c3~2',
						'FormButtons~'=>'c4~3'
					]);

		$user_model = $this->add('xavoc\ispmanager\Model_User');
		$user_model->title_field = "username";
		$user_model->addExpression('username')
			->set($user_model->dsql()->expr('CONCAT([0]," :: ",[1])',[$user_model->getElement('radius_username'),$user_model->getElement('name')]));

		$field_user_name = $form->addField('xepan\base\Basic','user_name');
		$field_user_name->setModel($user_model);
		$field_user_name->set($user_name);

		$form->addField('DatePicker','from_date')->set($from_date);
		$form->addField('DatePicker','to_date')->set($to_date);
		$form->addSubmit("Filter")->addClass('btn btn-primary btn-block');

		$model = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
		$model->addExpression('radius_username')
			->set($model->refSQL("user_id")->fieldQuery('radius_username'));
		$model->addExpression('sale_price')->set($model->refSQL('plan_id')->fieldQuery('sale_price'));
		$model->addExpression('last_invoice_date')->set(function($m,$q){
			$act = $m->add('xavoc\ispmanager\Model_Invoice')
					->addCondition('contact_id',$m->getElement('user_id'))
					->setOrder('id','desc')
					->setLimit(1);
			return $q->expr('IFNULL([0],0)',[$act->fieldQuery('created_at')]);
		});

		if($to_date)
			$model->addCondition('end_date','<=',$to_date);
		if($from_date)
			$model->addCondition('end_date','>=',$from_date);

		if($user_name){
			$model->addCondition('user_id',$user_name);
		}
		// ['from_date'=>$from_date,'to_date'=>$to_date,'customer_id'=>$user_name]
		// if($user_name){
		// 	$model->addCondition('customer_id',$user_name);
		// 	// $model->addCondition([['customer',$user_name],['radius_username',$user_name]]);
		// }

		$crud = $this->add('xepan\hr\CRUD',['allow_add'=>false,'fixed_header'=>false]);
		$crud->grid->fixed_header = false;
		// $crud->grid->addColumn('customer');
		// filter form submission
		if($form->isSubmitted()){
			$form->js(null,$crud->js()->reload(['from_date'=>$form['from_date'],'to_date'=>$form['to_date'],'user_name'=>$form['user_name']]))->univ()->execute();
		}

		$crud->grid->addHook('formatRow',function($g){

			if($g->current_customer != $g->model['user_id']){
				$g->current_row_html['user'] = $g->model['user']."<br/>( ".$g->model['radius_username']." )";
				$g->current_row['action'] = $g->model['action'];
				$g->current_customer = $g->model['user_id'];
				$g->skip_sno = false;
			}else{
				$g->current_row['user'] = "";
				$g->current_row_html['action'] = "";
				$g->skip_sno = true;
			}

			if($g->model['end_date'] == $g->model['last_invoice_date'])
				$g->current_row_html['last_invoice_date'] = "<div class='alert alert-success'>Yes, Invoice created <br/><strong>".$g->model['last_invoice_date']."</strong></div>";
			else
				$g->current_row_html['last_invoice_date'] = "<div class='alert alert-danger'>No, Last Invoice Date: <br/><strong>".$g->model['last_invoice_date']."</strong></div>";

		// 	if($g->current_invoice != $g->model['qsp_master_id']){

		// 		$g->current_row_html['qsp_master'] = $g->model['qsp_master']."<br/>".$g->model['qsp_status'];
		// 		$g->current_row['action'] = $g->model['action'];

		// 		$g->current_invoice = $g->model['qsp_master_id'];
		// 		$g->skip_sno = false;
		// 	}else{
		// 		$g->current_row['qsp_master'] = "";
		// 		$g->skip_sno = true;
		// 		$g->current_row_html['action'] = "";
		// 	}
			// $other_columns = ['plan'=>'<button>Create Invoice</button>','customer'=>''];
			// $g->insertBefore($other_columns);
		});

		$crud->grid->current_customer = null;
		$crud->grid->current_invoice = null;

		$crud->setModel($model,['user_id','user','radius_username','customer','plan','sale_price','start_date','end_date','expire_date','last_invoice_date']);
		$grid = $crud->grid;
		$grid->add('VirtualPage')
			->addColumn('create_invoice')
			->set(function($page){
	          	$id = $_GET[$page->short_name.'_id'];

	          	$model = $page->add('xavoc\ispmanager\Model_UserPlanAndTopup')->load($id);
	          	$return_data = $model->createInvoice();

	          	$invoice_model = $this->add('xepan\commerce\Model_SalesInvoice')
							->load($return_data['master_detail']['id']);
				
				$page->add('View')->set("You have successfully created Invoice for this user, you can edit too ");
				$page->add("Button")
						->addClass('btn btn-primary')
						->set('Edit Invoice')
						->js("click")
						->redirect($this->api->url('xepan_commerce_quickqsp', array("document_type" => 'SalesInvoice','action'=>'edit','document_id'=>$return_data['master_detail']['id'])));
				
				$page->add('xepan\commerce\View_QSP',['qsp_model'=>$invoice_model]);
			});

		$order = $grid->addOrder();
		// $order->move('name','after','customer');
		// $order->move('qty_unit','after','quantity');
		// $order->move('tax_percentage','after','qty_unit');
		// $order->move('new_invoice_price','after','price');
		// $order->now();

		$grid->addPaginator($ipp=25);
		$removeColumn = ['edit','delete','action','attachment_icon','user_id'];
		foreach ($removeColumn as $key => $field_name) {
			$grid->removeColumn($field_name);
		}
	}
}