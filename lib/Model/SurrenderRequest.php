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

		if($issued_items->count()->getOne()){
			$form = $page->add('Form');
			foreach ($issued_items as $issued_item){
				$item_field = $form->addField('DropDown','item_'.$issued_item['id'],'Issued Item');
				$item_field->setModel('xepan\commerce\Model_Store_Item');
				$item_field->set($issued_item['item_id']);

			}
		}

	}


}