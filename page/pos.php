<?php

namespace xavoc\ispmanager;

class page_pos extends \xepan\commerce\page_pos{

	function page_item(){

		$channel = $this->add('xepan\base\Model_Contact');
		$data = [];
		if(!$channel->loadLoggedIn('Channel')){
			echo json_encode($data);
			exit;
		}

		$item = $this->add('xavoc\ispmanager\Model_Channel_Plan');
		$item->addCondition('status','Published');
		$item->addCondition('channel_id',$channel->id);
		
		if(isset($_GET['term'])){
			$term = htmlspecialchars($_GET['term']);
			$item->addCondition('name','like',"%".$term."%");
			$item->setLimit(20);
		}

		$item = $item->getRows();

		$data = [];
		// if(isset($_GET['term'])){
		foreach ($item as $key => $value){
			$temp = [];
			$temp['id'] = $value['id'];
			$temp['name'] = $value['name'];
			$temp['value'] = $value['name'];
			$temp['price'] = $value['sale_price'];
			$temp['sku'] = $value['sku'];
			$temp['description'] = $value['description'];
			$temp['custom_field'] = '{}';
			$temp['read_only_custom_field'] = '{}';
			// $temp['read_only_custom_field'] = json_encode($this->getReadOnlyCustomField($value['id']));
			$temp['qty_unit_id'] = $value['qty_unit_id']?:0;
			$temp['qty_unit_group_id'] = $value['qty_unit_group_id']?:0;
			$temp['tax_id'] = 0;
			$temp['hsn_sac'] = $value['hsn_sac'];
			
			// $taxation = $value->applicableTaxation($_GET['country_id'],$_GET['state_id']);

			// if($taxation){
			// 	$temp['tax_id'] = $taxation['taxation_id'];
			// 	$temp['tax_percentage'] = $taxation['percentage'];
			// }
			$data[$key] = $temp;
		}
		
		echo json_encode($data);
		exit;
		// }
	}

	function page_contact(){

		$channel = $this->add('xepan\base\Model_Contact');
		$data = [];
		if(!$channel->loadLoggedIn('Channel')){
			echo json_encode($data);
			exit;
		}

		$document_type = $_GET['document_type'];
		$contact_model = $this->add('xavoc\ispmanager\Model_Channel_User');
		$contact_model->addCondition('channel_id',$channel->id);
		
		if(isset($_GET['term'])){
			$term = htmlspecialchars($_GET['term']);
			$contact_model->addCondition([['effective_name','like',"%".$term."%"],['user','like','%'.$term.'%']]);
		}

		$contact_model->setLimit(20);

		$data = [];
		foreach ($contact_model->getRows() as $key => $value){
			$temp = [];
			$temp['id'] = $value['id'];
			$temp['name'] = $value['organization']." ".$value['first_name']." ".$value['last_name']." ".$value['user'];
			
			$temp['first_name'] = $value['first_name'];
			$temp['last_name'] = $value['last_name'];
			$temp['organization'] = $value['organization'];
			$temp['address'] = $value['address'];
			$temp['city'] = $value['city'];
			$temp['pin_code'] = $value['pin_code'];
			$temp['code'] = $value['code'];
			
			// if(in_array($document_type, ['SalesOrder','SalesInvoice'])){
				$temp['billing_country_id'] = $value['billing_country_id']?:$value['country_id'];
				$temp['billing_state_id'] = $value['billing_state_id']?:$value['state_id'];
				$temp['billing_name'] = $value['billing_name'];
				$temp['billing_address'] = $value['billing_address']?:$value['address'];
				$temp['billing_city'] = $value['billing_city']?:$value['city'];
				$temp['billing_pincode'] = $value['billing_pincode']?:$value['pin_code'];

				$temp['shipping_country_id'] = $value['shipping_country_id']?:$value['country_id'];
				$temp['shipping_state_id'] = $value['shipping_state_id']?:$value['state_id'];
				$temp['shipping_name'] = $value['shipping_name'];
				$temp['shipping_address'] = $value['shipping_address']?:$value['address'];
				$temp['shipping_city'] = $value['shipping_city']?:$value['city'];
				$temp['shipping_pincode'] = $value['shipping_pincode']?:$value['pin_code'];

				$temp['same_as_billing_address'] = $value['same_as_billing_address'];
			// }
			$data[$key] = $temp;
		}

		echo json_encode($data);
		exit;
	}
}