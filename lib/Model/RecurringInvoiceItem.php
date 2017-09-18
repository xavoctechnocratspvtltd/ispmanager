<?php

 namespace xavoc\ispmanager;

 class Model_RecurringInvoiceItem extends \xepan\commerce\Model_QSP_Detail{

 	public $from_date;
 	public $to_date;
 	public $customer_id;

 	public $acl_type = 'RecurringInvoiceItem';
	public $status = ['All'];
	public $actions = ['All'=>['view','edit','delete','create_invoice']];
	public $acl = true;

	function init(){
		parent::init();

		$this->getElement('item_id')->caption('Plan');
		// if(!$this->to_date) $this->to_date = $this->app->today;
		if($this->from_date) $this->from_date = date("Y-m-d",strtotime($this->from_date));
			
		$this->addExpression('is_recurring')->set(function($m,$q){
			return $q->expr('IFNULL([0],0)',[$m->refSQL('item_id')->fieldQuery('is_renewable')]);
		})->type('boolean');

		$this->addExpression('renewable_value')->set(function($m,$q){
			return $m->refSQL('item_id')->fieldQuery('renewable_value');
		});
		$this->addExpression('renewable_unit')->set(function($m,$q){
			return $m->refSQL('item_id')->fieldQuery('renewable_unit');
		});

		$this->addExpression('created_at')->set(function($m,$q){
			return $m->refSQL('qsp_master_id')->fieldQuery('created_at');
		});

		$this->addExpression('is_invoice_date_first_to_first')->set(function($m,$q){
			$user = $m->add('xavoc\ispmanager\Model_User');
			$user->addCondition('id',$m->getElement('customer_id'));
			return $q->expr('IFNULL([0],0)',[$user->dsql()->fieldQuery('is_invoice_date_first_to_first')]);
		})->type('boolean');

		$this->addExpression('include_pro_data_basis')->set(function($m,$q){
			$user = $m->add('xavoc\ispmanager\Model_User');
			$user->addCondition('id',$m->getElement('customer_id'));
			return $q->expr('IFNULL([0],0)',[$user->dsql()->fieldQuery('include_pro_data_basis')]);
		});
		
		$this->addExpression('invoice_renewable_date')->set(function($m,$q){
			return $q->expr('DATE_FORMAT(DATE_ADD([invoice_created_at],INTERVAL [renewable_value] [renewable_unit]),"%Y-%m-%d")',['invoice_created_at'=>$m->getElement('created_at'),'renewable_value'=>$m->getElement('renewable_value'),'renewable_unit'=>'month']);
		})->type('datetime');

		$this->addExpression('invoice_recurring_date')->set(function($m,$q){
			// if invoice first date to date
				// return 01-m-d of invoice renewable date
			// else
				// invoice renewable date
			return $q->expr('IF([0],DATE_FORMAT([1],"%Y-%m-01"),[1])',[$m->getElement('is_invoice_date_first_to_first'),$m->getElement('invoice_renewable_date')]);
		});

		$this->addExpression('new_invoice_price')->set(function($m,$q){
			// if invoice first date to date
				// return item price
			// else
				// qsp detail price
			return $q->expr('IF([invoice_date_to_date],[item_price],[last_sale_price])',
				[
					'invoice_date_to_date'=>$m->getElement('is_invoice_date_first_to_first'),
					'item_price'=>$m->refSQL('item_id')->fieldQuery('sale_price'),
					'last_sale_price'=>$m->getElement('price')
				]);
		})->caption('recurring price');


		$this->addExpression('radius_username')->set(function($m,$q){
			$model = $m->add('xavoc\ispmanager\Model_User')
				->addCondition('id',$m->getElement('customer_id'))
				;

			return $q->expr('IFNULL([0],0)',[$model->fieldQuery('radius_username')]);
		});

		$this->addCondition('is_recurring',true);
		$this->addCondition([['recurring_qsp_detail_id',0],['recurring_qsp_detail_id',null]]);
		
		if($this->to_date)
			$this->addCondition('invoice_recurring_date','<=',$this->to_date);
		if($this->from_date)
			$this->addCondition('invoice_recurring_date','>=',$this->from_date);

		if($this['is_invoice_date_first_to_first']){
			$this->addCondition('include_pro_data_basis',['invoice_and_data_both','invoice_only']);
		}
		if($this->customer_id){
			$this->addCondition('customer_id',$this->customer_id);
		}

		$this->addExpression('status')->set('"All"');
		$this->setOrder('customer_id');
	}

	function page_create_invoice($page){

		$recu_items = $this->add('xavoc\ispmanager\Model_RecurringInvoiceItem',['customer_id'=>$this['customer_id']]);

		$user = $this->add('xavoc\ispmanager\Model_User')->load($this['customer_id']);
		$detail_data = [];
		
		$invoice_recurring_date = null;
		foreach ($recu_items as $key => $recu_item) {
			$item = [
						'item_id'=>$recu_item['item_id'],
						'price'=>$recu_item['new_invoice_price'],
						'quantity'=>$recu_item['quantity'],
						'taxation_id'=>$recu_item['tax_id'],
						'shipping_charge'=>$recu_item['shipping_charge'],
						'shipping_duration'=>$recu_item['shipping_duration'],
						'express_shipping_charge'=>$recu_item['express_shipping_charge'],
						'express_shipping_duration'=>$recu_item['express_shipping_duration'],
						'qty_unit_id'=>$recu_item['qty_unit_id'],
						'discount'=>$recu_item['discount'],
						'recurring_from_qsp_detail_id'=>$recu_item['id']
					];
			array_push($detail_data, $item);

			$invoice_recurring_date = $recu_item['invoice_recurring_date'];
		}

		$user->debug = false;
		$return_data = $user->createInvoice($detail_data,null,true,$invoice_recurring_date);
		
		$page->add('View')->set("You have successfully created Invoice for this user, you can edit too ");

		$page->add("Button")->set('Edit Invoice')
				->js("click")
				->redirect($this->api->url('xepan_commerce_quickqsp', array("document_type" => 'SalesInvoice','action'=>'edit','document_id'=>$return_data['master_detail']['id'])));


		$invoice_model = $this->add('xepan\commerce\Model_SalesInvoice')->load($return_data['master_detail']['id']);
		$page->add('xepan\commerce\View_QSP',['qsp_model'=>$invoice_model]);

	}
}