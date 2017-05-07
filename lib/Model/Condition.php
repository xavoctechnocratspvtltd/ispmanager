<?php

namespace xavoc\ispmanager;

class Model_Condition extends \xepan\base\Model_Table{ 
	public $table = "isp_condition";
	
	public $acl_type="ispmanager_plan";
	function init(){
		parent::init();

		$this->hasOne('xavoc\ispmanager\Plan','plan_id');
		
		$this->addField('remark');
		$this->addField('data_limit')->hint('Data Limit in HUman redable format 20gb, 1tb, 500mb');
		$this->addField('download_limit')->hint('Limit in KBPS');
		$this->addField('upload_limit')->hint('Limit in KBPS');
		$this->addField('fup_download_limit')->hint('Limit in KBPS');
		$this->addField('fup_upload_limit')->hint('Limit in KBPS');
		$this->addField('accounting_download_ratio')->hint('ratio in %')->defaultValue(100);
		$this->addField('accounting_upload_ratio')->hint('ratio in %')->defaultValue(100);

		$this->addField('is_data_carry_forward')->type('boolean')->defaultValue(false);
		$this->addField('start_time');//->type('time')->display(['form'=>'TimePicker']);
		$this->addField('end_time');//->type('time')->display(['form'=>'TimePicker']);
									
		// for factor day
		$this->addField('sun')->type('boolean')->defaultValue(true);
		$this->addField('mon')->type('boolean')->defaultValue(true);
		$this->addField('tue')->type('boolean')->defaultValue(true);
		$this->addField('wed')->type('boolean')->defaultValue(true);
		$this->addField('thu')->type('boolean')->defaultValue(true);
		$this->addField('fri')->type('boolean')->defaultValue(true);
		$this->addField('sat')->type('boolean')->defaultValue(true);

		$this->addField('d01')->type('boolean')->defaultValue(true);
		$this->addField('d02')->type('boolean')->defaultValue(true);
		$this->addField('d03')->type('boolean')->defaultValue(true);
		$this->addField('d04')->type('boolean')->defaultValue(true);
		$this->addField('d05')->type('boolean')->defaultValue(true);
		$this->addField('d06')->type('boolean')->defaultValue(true);
		$this->addField('d07')->type('boolean')->defaultValue(true);
		$this->addField('d08')->type('boolean')->defaultValue(true);
		$this->addField('d09')->type('boolean')->defaultValue(true);
		$this->addField('d10')->type('boolean')->defaultValue(true);
		$this->addField('d11')->type('boolean')->defaultValue(true);
		$this->addField('d12')->type('boolean')->defaultValue(true);
		$this->addField('d13')->type('boolean')->defaultValue(true);
		$this->addField('d14')->type('boolean')->defaultValue(true);
		$this->addField('d15')->type('boolean')->defaultValue(true);
		$this->addField('d16')->type('boolean')->defaultValue(true);
		$this->addField('d17')->type('boolean')->defaultValue(true);
		$this->addField('d18')->type('boolean')->defaultValue(true);
		$this->addField('d19')->type('boolean')->defaultValue(true);
		$this->addField('d20')->type('boolean')->defaultValue(true);
		$this->addField('d21')->type('boolean')->defaultValue(true);
		$this->addField('d22')->type('boolean')->defaultValue(true);
		$this->addField('d23')->type('boolean')->defaultValue(true);
		$this->addField('d24')->type('boolean')->defaultValue(true);
		$this->addField('d25')->type('boolean')->defaultValue(true);
		$this->addField('d26')->type('boolean')->defaultValue(true);
		$this->addField('d27')->type('boolean')->defaultValue(true);
		$this->addField('d28')->type('boolean')->defaultValue(true);
		$this->addField('d29')->type('boolean')->defaultValue(true);
		$this->addField('d30')->type('boolean')->defaultValue(true);
		$this->addField('d31')->type('boolean')->defaultValue(true);

		// $this->addField('is_recurring')->type('boolean');
		// if condition is recurring then show
		$this->addField('data_reset_value')->type('number')->defaultValue(0);
		$this->addField('data_reset_mode')->enum(['hours','days','months','years']);

		$this->addHook('beforeSave',$this);

		$this->add('xavoc\ispmanager\Controller_HumanByte')
			->handleFields([
					'data_limit',
					'download_limit',
					'upload_limit',
					'fup_download_limit',
					'fup_upload_limit'
				]);
		// $this->add('dynamic_model/Controller_AutoCreator');
	}

	function beforeSave(){
		if($this['start_time']=='') $this['start_time']=null;
		if($this['end_time']=='') $this['end_time']=null;
		if(!$this['data_reset_value']) $this['data_reset_value']=null;

		if($this['data_limit'] && (!$this['data_reset_value'] || !$this['data_reset_mode']))
			throw $this->exception('Value mandatory if having Data Limit','ValidityCheck')
						->setField(!$this['data_reset_value']?'data_reset_value':'data_reset_mode');
	}


}