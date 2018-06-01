<?php

namespace xavoc\ispmanager;


class page_upcominginvoice2 extends \xepan\base\Page {
	
	public $title ="Up-Coming Invoice";

	function init(){
		parent::init();

		$run_group =  $_GET['run_group']?:"yes";

		$this->app->stickyGET('filter');
		$from_date = $this->app->stickyGET('from_date')?:$this->app->today;
		// $from_date = $this->app->stickyGET('from_date')?:(date('Y-m-01',strtotime($this->app->today)));
		$to_date = $this->app->stickyGET('to_date')?:$this->app->today;
		$user_name = $this->app->stickyGET('user_name');
		$include_expired = $this->app->stickyGET('show_expired');
		
		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
				->addContentSpot()
				->makePanelsCoppalsible()
				->layout([
						'user_name'=>'Filter~c1~4',
						'from_date'=>'c2~2',
						'to_date'=>'c3~2',
						// 'include_expired'=>'c4~2',
						'FormButtons~'=>'c5~2'
					]);

		$user_model = $this->add('xavoc\ispmanager\Model_User');
		$user_model->title_field = "username";
		$user_model->addExpression('username')
			->set($user_model->dsql()->expr('CONCAT( IFNULL([0],"")," :: ",IFNULL([1],"")," :: ",IFNULL([2],""))',[$user_model->getElement('radius_username'),$user_model->getElement('name'),$user_model->getElement('organization')]));

		$field_user_name = $form->addField('xepan\base\Basic','user_name');
		$field_user_name->setModel($user_model);
		$field_user_name->set($user_name);

		$form->addField('DatePicker','from_date')->set($from_date);
		$form->addField('DatePicker','to_date')->set($to_date);
		// $form->addField('checkbox','include_expired')->set($include_expired);
		$form->addSubmit("Filter")->addClass('btn btn-primary btn-block');

		$model = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
		$model->addExpression('radius_username')
			->set($model->refSQL("user_id")->fieldQuery('radius_username'));
		$model->addExpression('organization')
			->set($model->refSQL("user_id")->fieldQuery('organization'));

		$model->addExpression('sale_price')->set($model->refSQL('plan_id')->fieldQuery('sale_price'));
		$model->addExpression('plan_code')->set($model->refSQL('plan_id')->fieldQuery('sku'));

		$model->addExpression('last_invoice_date')->set(function($m,$q){
			$act = $m->add('xavoc\ispmanager\Model_Invoice')
					->addCondition('contact_id',$m->getElement('user_id'))
					->setOrder('id','desc')
					->setLimit(1);
			return $q->expr('IFNULL([0],0)',[$act->fieldQuery('created_at')]);
		});
		$model->addExpression('last_invoice_no')->set(function($m,$q){
			$act = $m->add('xavoc\ispmanager\Model_Invoice')
					->addCondition('contact_id',$m->getElement('user_id'))
					->setOrder('id','desc')
					->setLimit(1);
			return $q->expr('CONCAT([0],[1])',[$act->fieldQuery('serial'),$act->fieldQuery('document_no')]);
		});
		$model->addExpression('user_status')->set($model->refSQL('user_id')->fieldQuery('status'));
		
		$model->addCondition('user_status','Active');

		if($to_date)
			$model->addCondition('end_date','<=',$to_date);
		if($from_date)
			$model->addCondition('end_date','>=',$from_date);

		// $new_model = clone($model);
		if($user_name){
			$model->addCondition('user_id',$user_name);
		}
		// if($include_expired != "true"){
		// 	$model->addCondition('is_expired','<>',true);
		// }

		if($run_group == "yes"){
			$model->_dsql()->where('id in ( select max(id) from isp_user_plan_and_topup group by user_id)');
		}

		$crud = $this->add('xepan\hr\CRUD',['allow_add'=>false,'fixed_header'=>false]);
		$crud->grid->fixed_header = false;
		$crud->grid->add('misc\Export');

		// if($_GET['filter']){
		// $new_model->addCondition('is_expired',true);
		// $crud->grid->add('View',null,'quick_search')->set('Expired Plan Count: '.$new_model->count()->getOne())->addClass('label label-info');
		// }
		// $crud->grid->addColumn('customer');
		// filter form submission
		if($form->isSubmitted()){
			$form->js(null,$crud->js()->reload(['filter'=>1,'from_date'=>$form['from_date'],'to_date'=>$form['to_date'],'user_name'=>$form['user_name'],'show_expired'=>$form['include_expired']]))->univ()->execute();
		}



		$crud->grid->addHook('formatRow',function($g){

			if($g->current_customer != $g->model['user_id']){
				$g->current_row_html['user'] = $g->model['user']."<br/>( ".$g->model['organization']." )"."<br/> <p class='label label-info'>".$g->model['user_status']."</p>"."<br/>radius_username = ".$g->model['radius_username'];
				$g->current_row['action'] = $g->model['action'];
				$g->current_customer = $g->model['user_id'];
				$g->skip_sno = false;
			}else{
				$g->current_row['user'] = "";
				$g->current_row_html['action'] = "";
				$g->skip_sno = true;
			}


			if(strtotime($g->model['end_date']) == strtotime(date('Y-m-d',strtotime($g->model['last_invoice_date']))))
				$g->current_row_html['last_invoice_date'] = "<div class='alert alert-success'>Yes, Invoice created <br/>Invoice No.".$g->model['last_invoice_no']."<br/><strong>".date('d-M-Y',strtotime($g->model['last_invoice_date']))."</strong></div>";
			else
				$g->current_row_html['last_invoice_date'] = "<div class='alert alert-danger'>No, Last Invoice Date: <br/>Invoice No.".$g->model['last_invoice_no']."<br/><strong>".$g->model['last_invoice_date']."</strong></div>";
				

			$g->current_row_html['end_date'] = "<div class='alert alert-danger'><strong>".date('d-M-Y',strtotime($g->model['end_date']))."</strong></div>";
			
			$g->current_row_html['plan'] = $g->model['plan']."<br/>".$g->model['plan_code']."<br/>"."Sale Price: ".$g->model['sale_price'];
			if($g->model['is_expired']){
				$g->setTDParam('plan','class',"red-bg");
			}else{
				$g->setTDParam('plan','class',"");
			}
			
		});

		$crud->grid->current_customer = null;
		$crud->grid->current_invoice = null;

		$crud->setModel($model,['user_id','user','radius_username','customer','plan','plan_code','sale_price','start_date','end_date','expire_date','last_invoice_date','organization','user_status','last_invoice_no','is_expired']);
		$crud->grid->removeColumn('organization');
		$grid = $crud->grid;
		$grid->add('VirtualPage')
			->addColumn('create_invoice')
			->set(function($page){
	          	$id = $_GET[$page->short_name.'_id'];

	          	$model = $page->add('xavoc\ispmanager\Model_UserPlanAndTopup');
	          	$model->addExpression('last_invoice_date')->set(function($m,$q){
					$act = $m->add('xavoc\ispmanager\Model_Invoice')
							->addCondition('contact_id',$m->getElement('user_id'))
							->setOrder('id','desc')
							->setLimit(1);
					return $q->expr('IFNULL([0],0)',[$act->fieldQuery('created_at')]);
				});
	          	$model->load($id);

	          	if(strtotime($model['end_date']) == strtotime(date('Y-m-d',strtotime($model['last_invoice_date'])))){
	          		$page->add('View')->addClass('alert alert-danger')->set('Invoice Already Created');
	          		return;
	          	}
			
				
	          	$return_data = $model->createInvoice();

	          	$invoice_model = $this->add('xepan\commerce\Model_SalesInvoice')
							->load($return_data['master_detail']['id']);
				
				$page->add('View')->set("You have successfully created Invoice for this user, you can edit too ");
				$page->add("Button")
						->addClass('btn btn-primary')
						->set('Edit Invoice')
						->js("click")
						->redirect($this->api->url('xepan_commerce_quickqsp', array("document_type" => 'SalesInvoice','action'=>'edit','document_id'=>$return_data['master_detail']['id'])));
				$v = $page->add('View');
				$v->add('xepan\commerce\page_quickqsp',['document_id'=>$invoice_model->id,'document_type'=>'SalesInvoice','readmode'=>true]);
				// $page->add('xepan\commerce\View_QSP',['qsp_model'=>$invoice_model]);
			});

		$order = $grid->addOrder();

		$grid->addColumn('Button','set_end_date_to_invoice');
		if( $uid = $_GET['set_end_date_to_invoice']){
			$model->load($uid);
			$inv = $this->add('xavoc\ispmanager\Model_Invoice')
					->addCondition('contact_id',$model['user_id'])
					->setOrder('id','desc')
					->tryLoadAny();
			if(!$inv->loaded()){
				$grid->js(null,$grid->js()->univ()->errorMessage('first create invoice'))->reload()->execute();
			}else{
				$inv['created_at'] = $model['end_date'];
				$inv->save();
				$grid->js(null,$grid->js()->univ()->successMessage('updated'))->reload()->execute();
			}

		}
		$grid->addPaginator($ipp=25);
		$removeColumn = ['edit','delete','action','attachment_icon','user_id','plan_code','last_invoice_no','sale_price','user_status','radius_username'];
		foreach ($removeColumn as $key => $field_name) {
			$grid->removeColumn($field_name);
		}

	}
}