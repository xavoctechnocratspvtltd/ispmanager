<?php

namespace xavoc\ispmanager;


class page_upcominginvoice extends \xepan\base\Page {
	
	public $title ="Up-Coming Invoice";

	function init(){
		parent::init();

		$model = $this->add('xavoc\ispmanager\Model_RecurringInvoiceItem');
		$grid = $this->add('Grid');
		
		$this->customer_list = [];
		$grid->addHook('formatRow',function($g){
			
			if(!isset($this->customer_list[$g->model['customer_id']])){
				$this->customer_list[$g->model['customer_id']] = $g->model['customer_id'];
				$g->current_row['customer'] = $g->model['customer'];
			}else{
				$g->current_row['customer'] = "";
			}
		});

		$grid->setModel($model);
		$order = $grid->addOrder();
		$order->move('customer','first');
		$order->move('name','after','customer');
		$order->move('qty_unit','after','quantity');
		$order->move('tax_percentage','after','qty_unit');
		$order->move('new_invoice_price','after','price');
		$order->now();

		$removeColumn = ['customer_id','qsp_master','item_template_design','rate','item_designer','item_nominal','total_amount','extra_info','customer_id','qsp_status','name','shipping_charge','shipping_duration','express_shipping_charge','express_shipping_duration','is_shipping_inclusive_tax','amount_excluding_tax','amount_excluding_tax_and_shipping','item_designer_id','item_nominal_id','item_qty_unit_id','item_qty_unit','qsp_type','sub_tax','renewable_value','renewable_unit','narration','tax_amount','taxation','is_invoice_date_first_to_first','invoice_renewable_date','invoice_recurring_date','include_pro_data_basis','created_at'];
		foreach ($removeColumn as $key => $field_name) {
			$grid->removeColumn($field_name);
		}
	}
}