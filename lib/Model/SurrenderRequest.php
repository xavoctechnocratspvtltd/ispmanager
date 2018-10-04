<?php

namespace xavoc\ispmanager;
class Model_SurrenderRequest extends \xepan\base\Model_Table{

	public $table = 'isp_surrender_request';
	public $acl_type = "SurrenderRequest";
	public $status = ["SurrenderRequest","SurrenderDeviceReceived","Surrender"];
	public $actions = [
			"SurrenderRequest"=>['view','surrender_device_received','edit','delete'],
			"SurrenderDeviceReceived"=>['view','surrender','edit','delete'],
			"Surrender"=>['view','surrender','edit','delete']
		];
	public $status_color = [
						'SurrenderRequest'=>'danger',
						'SurrenderDeviceReceived'=>'primary',
						'Surrender'=>'success'
					];

	function init(){
		parent::init();
		
		$this->hasOne('xepan\commerce\Customer','contact_id','unique_name')->display(['form'=>'xepan\base\DropDown'])->caption('Customer');
		$this->hasOne('xepan\hr\Employee','assign_to_id');
		$this->hasOne('xepan\base\Model_Contact','created_by_id')->defaultValue($this->app->employee->id)->system(true);

		$this->addField('device_collection_availibility')->display(['form'=>'DateTimePicker'])->type('datetime');
		$this->addField('narration')->type('text');
		$this->addField('status')->enum($this->status)->defaultValue('SurrenderRequest');
		$this->addField('created_at')->type('datetime')->display(['form'=>'DateTimePicker'])->defaultValue($this->app->now);
		$this->addField('device_collected_at')->type('datetime')->display(['form'=>'DateTimePicker'])->system(true);
		$this->addField('surrender_at')->type('datetime')->display(['form'=>'DateTimePicker'])->system(true);
		$this->addField('device_collection_data')->type('text')->system(true);

		$this->addExpression('duration_in_month')->set(function($m,$q){
			return $q->expr('TIMESTAMPDIFF(MONTH,[0],IF([1],[1],[2]))',[$m->getElement('created_at'),$m->getElement('surrender_at'),"'".$this->app->now."'"]);
		});
		
		$this->add('xepan\base\Controller_AuditLog');
		$this->is([
			'contact_id|to_trim|required'
		]);
	}

	function page_surrender_device_received($page){
		if(!$this['duration_in_month']){
			$date_diff = $this->app->my_date_diff(($this['surrender_at']?$this['surrender_at']:$this->app->today),date('Y-m-d',strtotime($this['created_at'])));
			$page->add('View')->setHtml('<h3>Surrender 1 month notice period is not surved, Total Days Served: '.$date_diff['days']." Applied on: ".$this['created_at']."</h3>")->addClass('bg bg-warning');
		} 

		$user_model = $this->add('xavoc\ispmanager\Model_User')->load($this['contact_id']);
		$issued_items = $user_model->getIssuedDevices();

		// $this->app->print_r($issued_items->getRows(),true);
		if($issued_items->count()->getOne()){
			$form = $page->add('Form');
			$form->addField('DropDown','warehouse','Device Submit To Warehouse')
				->setEmptyText('Please Select ...')
				->validate('required')
				->setModel('xepan\commerce\Model_Store_Warehouse');

			$form->add('HR');
			foreach ($issued_items as $issued_item){
				$item_field = $form->addField('DropDown','item_'.$issued_item['id'],'Issued Item');
				$item_field->setModel('xepan\commerce\Model_Store_Item');
				$item_field->set($issued_item['item_id']);
				if(count(json_decode($issued_item['serial_nos'],true)) > 0){
					$item_field->setFieldHint($issued_item['serial_nos']);
					$form->addField('Text','damaged_'.$issued_item['id'],'damaged_devices');
				}else{
					$form->addField('DropDown','received_status_'.$issued_item['id'],'received_status')->setValueList(['ok'=>'Ok','damaged'=>'Damaged']);
				}
				$form->add('HR');

			}

			$form->addSubmit('Receive');

			if($form->isSubmitted()){
				// check if damaged serial numbers are the ones from issued
				foreach ($issued_items as $issued_item){
					$serials = json_decode($issued_item['serial_nos'],true);
					if(count($serials) > 0){
						$damaged_items = array_map('trim',explode(",", $form['damaged_'.$issued_item['id']]));
						if(count($damaged_items) == 1 && $damaged_items[0]=='') unset($damaged_items[0]);

						if(count(array_intersect($damaged_items, $serials)) != count($damaged_items)){
							$form->displayError('damaged_'.$issued_item['id'],'Serial numbers are not in issues list');
						}
					}

				}
				
				// receive all items and add to issue_submit transaction
				$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse',['use_contact'=>true])
					->load($this['contact_id']);
				$transaction = $warehouse->newTransaction(null,null,$this['contact_id'],'Issue_Submitted',null,$form['warehouse'],"Surreder Device Received",null,'Received',$this->app->now);

				foreach ($issued_items as $model){
					$item_model = $this->add('xepan\commerce\Model_Item')
							->load($model['item_id']);

					// check serial no exist or not in department
					$result_data = [];

					// $senitized_serial_nos = $code = preg_replace('/\n$/','',preg_replace('/^\n/','',preg_replace('/[\r\n]+/',"\n",$model['serial_nos'])));
					$senitized_serial_nos = json_decode($model['serial_nos'],true);
					$stock_data = $item_model->getStockAvalibility(($model['extra_info']?:'{}'),$model['quantity'],$result_data,$this['contact_id'],$item_model['qty_unit_id'],$senitized_serial_nos);
					$cf_key = $item_model->convertCustomFieldToKey(json_decode($model['extra_info']?:'{}',true));

					if($item_model['is_serializable'] && isset($stock_data[$item_model['name']][$cf_key]['serial']) && count($stock_data[$item_model['name']][$cf_key]['serial']['unavailable']) ){
						$form->js()->univ()->errorMessage('Serial nos not found in '.$warehouse['name'] . ' => '. implode(",", $stock_data[$item_model['name']][$cf_key]['serial']['unavailable']))->execute();
					}
					$serial_fields=[
						'contact_id'=>$form['warehouse'],
						'transaction_id'=>$transaction->id
					];
					$transaction->addItem(null,$model['item_id'],$model['quantity'],null,$cf_key,'Received',null,null,null,$senitized_serial_nos,null,"Surreder Device Received",$serial_fields);
				}

				$this['status'] = "SurrenderDeviceReceived";
				$this['device_collection_data'] = json_encode($form->get());
				$this->save();
				return $page->js()->univ()->closeDialog();
			}


		}

	}


}