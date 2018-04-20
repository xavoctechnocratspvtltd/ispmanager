<?php

namespace xavoc\ispmanager;

class Model_RadReply extends \xepan\base\Model_Table{ 
	public $table = "radreply";
	
	function init(){
		parent::init();

		$this->addField('username');
		$this->addField('attribute');
		$this->addField('op');
		$this->addField('value');

		$this->addHook('beforeSave',$this);
	}

	function beforeSave(){
		$this['value'] = trim($this['value']);
		
		if($this['attribute'] == "Framed-IP-Address"){
			$old_model = $this->add('xavoc\ispmanager\Model_RadReply');
			$old_model->addCondition('op',$this['op']);
			$old_model->addCondition('attribute',$this['attribute']);
			$old_model->addCondition('value',trim($this['value']));
			$old_model->addCondition("id",'<>',$this->id);
			$old_model->tryLoadAny();
			
			if($old_model->loaded())
				throw $this->exception($old_model['attribute'].' Value is already exist with user '.$old_model['username'], 'ValidityCheck')
					->setField('value');
		}
			

	}
}