<?php

 namespace xavoc\ispmanager;

 class Model_RecurringInvoiceItem extends \xepan\commerce\Model_QSP_Detail{

 	public $on_date;

	function init(){
		parent::init();

		if(!$this->on_date) $this->on_date = $this->app->today;

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
			return $q->expr('DATE_ADD([invoice_created_at],INTERVAL [renewable_value] [renewable_unit])',['invoice_created_at'=>$m->getElement('created_at'),'renewable_value'=>$m->getElement('renewable_value'),'renewable_unit'=>'month']);
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
		});

		$this->addCondition('is_recurring',true);
		$this->addCondition([['recurring_qsp_detail_id',0],['recurring_qsp_detail_id',null]]);
		$this->addCondition('invoice_recurring_date','<=',$this->on_date);
		$this->addCondition('include_pro_data_basis',['invoice_and_data_both','invoice_only']);
		
		$this->setOrder('customer_id');
	}
}