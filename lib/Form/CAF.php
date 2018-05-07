<?php

namespace xavoc\ispmanager;

class Form_CAF extends \Form{
	public $model;
	public $mandatory_field = [];  // field_name => validation rule
	public $validate_values = false;  // field_name => validation rule
	public $manage_consumption = true;
	public $show_consumption_detail=false;
	public $session_item;
	public $allow_invoice=false;
	public $invoice_items=false;
	public $show_demoplan=false;
	public $change_plan=true;
	public $show_reset_plan_detail = true;
	function init(){
		parent::init();
		
		if(!$this->model)
			$this->model = $model = $this->add('xavoc\ispmanager\Model_User');

		// $config = $this->add('xepan\base\Model_ConfigJsonModel',
			// [
			// 	'fields'=>[
			// 				'attachment_type'=>'text'
			// 			],
			// 		'config_key'=>'ISPMANAGER_MISC',
			// 		'application'=>'ispmanager'
			// ]);
		// $config->add('xepan\hr\Controller_ACL');
		// $config->tryLoadAny();
		// $this->attachment_type = $attachment_type = [];
		// $this->attachment_fields = $attachment_fields = [];
		// if($config['attachment_type']){
		// 	$attachment_type = explode(",", $config['attachment_type']);
		// 	foreach ($attachment_type as $key => $value) {
		// 		$attachment_fields[$this->app->normalizeName($value)] = "c1~12";
		// 	}
		// }

		$model_layout_fields = [
					'first_name'=>'Basic Detail~c1~4',
					'last_name'=>'c2~4',
					'organization'=>'c3~4',

					'tin_no'=>'c6~3',
					'pan_no'=>'c7~3',
					'gstin'=>'c8~3',
					'website'=>'c9~3',
					'connection_type'=>'c10~3',
					'image_id~Photo'=>'c11~3',
					'customer_type'=>'c12~3',

					'shipping_country_id~Country'=>'Service Installation Address~b1~6',
					'shipping_state_id~State'=>'b1~6',
					'shipping_city~City'=>'b1~6',
					'shipping_pincode~Pincode'=>'b1~6',
					'shipping_address~Address'=>'b2~6',
					'same_as_billing_address~Billing Address Same as Installation Address'=>'b2~6',

					'billing_country_id'=>'Billing Address~b1~6~closed',
					'billing_state_id'=>'b1~6',
					'billing_city'=>'b1~6',
					'billing_pincode'=>'b1~6',
					'billing_address'=>'b2~6',

					'plan'=>'Plan/ Radius Information~c1~4',
					'radius_username'=>'c2~4',
					'radius_password'=>'c3~4',
					'mac_address'=>'c4~4',
					'simultaneous_use'=>'c5~4',
					'grace_period_in_days'=>'c6~4',
					'demo_plan~Demo Plan'=>'c7~4',
					'reset_same_plan_again'=>'c8~4',
					'reset_same_plan_again_on_date'=>'c9~4',

					'consumptions~'=>'Consumptions~c1-12',
					'documents~'=>'Documents~c1-12',
				];

		$field_array = ['first_name','last_name','organization','customer_type','image_id','tin_no','pan_no','gstin','website','shipping_country_id','shipping_state_id','shipping_city','shipping_address','shipping_pincode','same_as_billing_address','billing_country_id','billing_state_id','billing_city','billing_pincode','billing_address','plan','plan_id','radius_username','radius_password','mac_address','simultaneous_use','grace_period_in_days','create_invoice','is_invoice_date_first_to_first','include_pro_data_basis','connection_type','demo_plan_id'];
		$field_array = array_combine($field_array,$field_array);

		$invoice_array = [];
		if($this->allow_invoice){
			$invoice_array = [
				'create_invoice~'=>'Invoice~c1~4',
				'is_invoice_date_first_to_first~'=>'c2~4',
				'include_pro_data_basis'=>'c3~4',
				'invoice_items~'=>'c4~12'
			];
		}else{
			unset($field_array['create_invoice']);
			unset($field_array['is_invoice_date_first_to_first']);
			unset($field_array['include_pro_data_basis']);
			unset($field_array['invoice_items']);
		}

		if(!$this->show_reset_plan_detail){
			unset($model_layout_fields['reset_same_plan_again']);
			unset($model_layout_fields['reset_same_plan_again_on_date']);
		}

		$layout_array = array_merge($model_layout_fields,$invoice_array);


		$this->add('xepan\base\Controller_FLC')
				->addContentSpot()
				->makePanelsCoppalsible()
				->layout($layout_array);

		$this->setModel($this->model,$field_array);

		if(!$this->show_demoplan){
			$this->getElement('demo_plan')->setAttr('disabled');
			// unset($layout_array['demo_plan~Demo Plan']);
			// unset($field_array['demo_plan_id']);
		}
		$this->getElement('plan_id')->getModel()->addCondition('status','Published');
		if($this->allow_invoice){
			$this->getElement('create_invoice')->set(0);
			$this->getElement('is_invoice_date_first_to_first')->set(0);
			$this->getElement('include_pro_data_basis')->set('none');
		}

		if($this->show_reset_plan_detail){
			$is_reset_field = $this->addField('checkbox','reset_same_plan_again');
			$this->addField('DatePicker','reset_same_plan_again_on_date');
			$is_reset_field->js(true)->univ()->bindConditionalShow([
				'1'=>['reset_same_plan_again_on_date']
			],'div.col-md-4');

		}

		// foreach ($attachment_type as $key => $value) {
		// 	$attachment_name = $this->app->normalizeName($value);

		// 	$field = $this->addField('xepan\base\Upload',$attachment_name,$value);
		// 	$field->setModel('xepan\filestore\Image');

		// 	$attachment = $this->add('xavoc\ispmanager\Model_Attachment');
		// 	$attachment->addCondition('contact_id',$this->model->id);
		// 	$attachment->addCondition('title',$attachment_name);
		// 	$attachment->tryLoadAny();

		// 	$field->set($attachment['file_id']);
		// }
		// billing address
		$country_field =  $this->getElement('billing_country_id');
		$country_field->getModel()->addCondition('status','Active');
		$state_field = $this->getElement('billing_state_id');
		$state_field->getModel()->addCondition('status','Active');
		if($country_id = $this->app->stickyGET('country_id')){
			$state_field->getModel()->addCondition('country_id',$country_id);
		}
		// $country_field->js('change',$form->js()->atk4_form('reloadField','state_id',[$this->app->url(),'country_id'=>$state_field->js()->val()]));
		$country_field->js('change',$state_field->js()->reload(null,null,[$this->app->url(null,['cut_object'=>$state_field->name]),'country_id'=>$country_field->js()->val()]));

		// shipping address
		$s_country_field =  $this->getElement('shipping_country_id');
		$s_country_field->getModel()->addCondition('status','Active');
		$s_state_field = $this->getElement('shipping_state_id');
		$s_state_field->getModel()->addCondition('status','Active');

		if($s_country_id = $this->app->stickyGET('s_country_id')){
			$s_state_field->getModel()->addCondition('s_country_id',$s_country_id);
		}
		// $country_field->js('change',$form->js()->atk4_form('reloadField','state_id',[$this->app->url(),'country_id'=>$state_field->js()->val()]));
		$s_country_field->js('change',$s_state_field->js()->reload(null,null,[$this->app->url(null,['cut_object'=>$s_state_field->name]),'country_id'=>$s_country_field->js()->val()]));

		// mandatory field 
		foreach ($this->mandatory_field as $field_name => $validation) {
			$this->getElement($field_name)->validate($validation);
		}

		if($this->manage_consumption){
			// session model
			$session_item = $this->session_item = $this->add('Model',['table'=>'item']);
			$session_item->setSource('Session');

			$session_item->addField('item')->display(['form'=>'xepan\commerce\Item'])->setModel('xepan\commerce\Model_Store_Item');
			$session_item->addField('item_name');
			$session_item->addHook('afterLoad',function($m){$m['item_name'] = $this->add('xepan\commerce\Model_Store_Item')->load($m['item'])->get('name'); });
		
			$session_item->addField('unit');
			$session_item->addField('quantity')->type('number');
			$session_item->addField('extra_info')->type('text');
			$session_item->addField('narration')->type('text');
			$session_item->addField('serial_nos')->type('text')->hint('Enter Seperated');

			$session_item->addHook('beforeSave',function($m){
				
				$oi = $this->add('xepan\commerce\Model_Item')->load($m['item']);
				$serial_no_array = [];
				if($oi['is_serializable']){
		          $code = preg_replace('/\n$/','',preg_replace('/^\n/','',preg_replace('/[\r\n]+/',"\n",$m['serial_nos'])));
		          $serial_no_array = explode("\n",trim($code));
		          if($serial_no_array[0] == "" || $m['quantity'] != count($serial_no_array))
		          	throw $this->exception('count of serial nos must be equal to receive quantity','ValidityCheck')->setField('serial_nos');
		        }
		        $m['unit'] = $oi['qty_unit'];
			});

			$crud = $this->layout->add('CRUD',['entity_name'=>'Consumed Item'],'consumptions');
			$crud->setModel($session_item,['item','quantity','extra_info','narration','serial_nos'],['item_name','quantity','unit','extra_info','narration','serial_nos']);
		}

		if($this->show_consumption_detail){
			$stock_model = $this->add('xepan\commerce\Model_Item_Stock',['warehouse_id'=>$this->model->id]);
			$stock_model->addCondition('maintain_inventory',true);

			$grid= $this->layout->add('xepan\base\Grid',['fixed_header'=>false],'consumptions');
			$grid->setModel($stock_model,['name','net_stock','serial_nos','qty_unit']);
			$grid->addPaginator(10);
		}

		$attachment_model = $this->add('xavoc\ispmanager\Model_Attachment');
		$attachment_model->addCondition('contact_id',$this->model->id);

		$crud = $this->layout->add('CRUD',null,'documents');
		$crud->setModel($attachment_model,['title','file_id','thumb_url'],['title','thumb_url']);

		$crud->grid->addHook('formatRow',function($g){
			$g->current_row_html['thumb_url'] = "<img style='width:150px;' src='".$g->model['thumb_url']."'>";
		});


		if($this->allow_invoice){
			$this->invoice_items = $this->add('Model');
			$this->invoice_items->setSource('Session');

			$item_model = $this->add('xepan\commerce\Model_Item')
				->addCondition('is_saleable',true)
				->addCondition('is_renewable',false);
			
			$this->invoice_items->addField('item')->display(['form'=>'DropDown'])
				->setModel($item_model);
			$this->invoice_items->addField('item_name');
			$this->invoice_items->addHook('afterLoad',function($m){$m['item_name'] = $this->add('xepan\commerce\Model_Item')->load($m['item'])->get('name'); });
		
			$this->invoice_items->addField('amount')->type('number');
			$this->invoice_items->addField('narration')->type('text');
			$crud = $this->layout->add('CRUD',['entity_name'=>'Invoice item'],'invoice_items');
			$crud->setModel($this->invoice_items,['item','amount','narration'],['item_name','amount','narration']);
		}

		if(!$this->change_plan){
			$this->getElement('plan')->setAttr('disabled');
		}
		
		$this->addSubmit('Save')->addClass('btn btn-primary btn-block');
		
	}


	function process(){
		if($this->isSubmitted()){

			if($this->validate_values){
				
				$validity_model = $this->add('xavoc\ispmanager\Model_Config_Mendatory');
				$validity_data = $validity_model->getFields('customer_type',$this['customer_type']);
				
				// mandatory fields 
				foreach ($validity_data['mendatory_fields'] as $field) {
					if(!$this[$field]) $this->displayError($field,'value must not be empty');
				}


				foreach ($validity_data['mendatory_documents'] as $field) {
					$attach = $this->add('xavoc\ispmanager\Model_Attachment')
						->addCondition('contact_id',$this->model->id)
						->addCondition('title',$field)
						->tryLoadAny();
					if(!$attach->loaded()) $this->js()->univ()->errorMessage('Document '.$field." required")->execute();
				}
				// $attachment_model->addCondition('contact_id',$this->model->id);
				// $all_titles_uploaded = array_column($attachment_model->getRows(), 'title');
				// foreach ($this->getValidation($this->model['customer_type']) as $rf) {
				// 	if(!in_array($rf, $all_titles_uploaded)) {
				// 		$this->js()->univ()->errorMessage('Required document '. $rf.' not found, cannot proceed');
				// 	}
				// }

			}

			if($this->show_reset_plan_detail){
				$this->app->reset_same_plan_again = $this['reset_same_plan_again'];
				$this->app->reset_same_plan_again_on_date = $this['reset_same_plan_again_on_date'];
			}

			try{
				$this->app->db->beginTransaction();	
				$this->hook('CAF_BeforeSave',[$this]);
				$this->save();

				//consumption entry
				if($this->manage_consumption == true){
					// temporary removed .. to do uncomment
					// if(!$this->session_item->count()){
					// 	$this->js()->univ()->errorMessage('please add consumption items')->execute();
					// }
					
					$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse');
					$transaction = $warehouse->newTransaction(null,null,$this->app->employee->id,'Issue',null,$this->model->id,"Issue to customer ".$this->model['name'],null,'Received',$this->app->now);
					foreach ($this->session_item as $model){
						$item_model = $this->add('xepan\commerce\Model_Item')
								->load($model['item']);

						// check serial no exist or not in department
						$result_data = [];
						$senitized_serial_nos = $code = preg_replace('/\n$/','',preg_replace('/^\n/','',preg_replace('/[\r\n]+/',"\n",$model['serial_nos'])));
						$stock_data = $item_model->getStockAvalibility(($model['extra_info']?:'{}'),$model['quantity'],$result_data,$this->app->employee->id,$item_model['qty_unit_id'],explode("\n",$senitized_serial_nos));
						
						$cf_key = $item_model->convertCustomFieldToKey(json_decode($model['extra_info']?:'{}',true));
						if($item_model['is_serializable'] && isset($stock_data[$item_model['name']][$cf_key]['serial']) && count($stock_data[$item_model['name']][$cf_key]['serial']['unavailable']) ){
							$this->js()->univ()->errorMessage('Serial nos not found in '.$this->app->employee['name'] . ' => '. implode(",", $stock_data[$item_model['name']][$cf_key]['serial']['unavailable']))->execute();
						}
						$serial_fields=[
							'contact_id'=>$this->model->id,
							'transaction_id'=>$transaction->id
						];

						$transaction->addItem(null,$model['item'],$model['quantity'],null,$cf_key,'Received',null,null,null,$senitized_serial_nos,null,$model['narration'],$serial_fields);
					}
				}


				// // attachment entry
				// if(isset($this->attachment_type)){
				// 	foreach ($this->attachment_type as $key => $value) {
				// 		$attachment_name = $this->app->normalizeName($value);
				// 		if($this[$attachment_name]){
				// 			$attachment = $this->add('xavoc\ispmanager\Model_Attachment');
				// 			$attachment->addCondition('contact_id',$this->model->id);
				// 			$attachment->addCondition('title',$attachment_name);
				// 			$attachment->tryLoadAny();
							
				// 			$attachment['file_id'] = $this[$attachment_name];
				// 			$attachment->save();
				// 		}
				// 	}
				// }
				$this->hook('CAF_AfterSave',[$this]);

				$this->app->db->commit();
			}catch(\Exception $e){
				$this->app->db->rollback();
				throw $e;
			}


			return $this->app->js(true,$this->js()->univ()->closeDialog())->univ()->successMessage('Installation done');
		}
	}

	function getValidation($type){
		// individual=>pancard,id_proof,address_proof,installation_address_proof#partnership_firm=>firm_pan_card,registration_certificate,firm_address_proof,authorized_signatory_id_proof,authorized_signatory_address_proof,installation_address_proof_if_different_from_deed#company=>company_pan_card,registration_certificate,company_address_proof,registration_certificate,address_proof,id_proof_authorized_signatory,address_proof_authorized_signatory,authority_letter,installation_address_proof_if_different_from_permanent_address

		$t= [
			'individual'=>['pan card',''],
			'partnership_firm'=>[],
			'company'=>[],
			'educational_institute'=>[],
			'educational_institute'=>[],
			'trust'=>[],
			'government_body'=>[],
			'proprietorship'=>[],
			'other'=>[],
		];

		return $t[$type];
	}
}