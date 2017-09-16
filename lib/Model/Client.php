<?php

namespace xavoc\ispmanager;

class Model_Client extends \xepan\base\Model_Table{ 
	public $table = "isp_client";
	public $acl_type="ispmanager_client";

	public $status = ['Active','Deactive'];
 	public $actions = [
 						'Active'=>['view','edit','delete','get_config','deactivate'],
 						'Deactive'=>['view','edit','delete','activate']
 					];

	function init(){
		parent::init();

		$this->addField('name');
		$this->addField('ipaddr')->caption('IP Address of Router');
		$this->addField('secret');

		$this->addField('status')->defaultValue('Active');

		$this->is([
			'name|to_trim|required',
			'ipaddr|to_trim|required',
			'secret|to_trim|required'
			]);

		$this->addHook('beforeSave',$this);
		$this->add('dynamic_model/Controller_AutoCreator');
	}

	function beforeSave(){

		$old_model = $this->add('xavoc\ispmanager\Model_Client');
		$old_model->addCondition([
					['name',$this['name']],
					['ipaddr',$this['ipaddr']],
				])
				->addCondition('id','<>',$this->id)
				;

		$old_model->tryLoadany();

		if($old_model->loaded()){
			throw $this->exception('Client name/IP Address Already added ','ValidityCheck')
						->setField('name');
		}

	}

	function deactivate(){
		$this['status'] = "Deactive";
		$this->save();
	}

	function activate(){
		$this['status'] = "Active";
		$this->save();
	}

	function page_get_config($page){	

		$form = $page->add('Form');
		$form->addField('text','config')->set($this->getConfig());
	}

	function getConfig(){

		$config = "\nClient ".$this['name']."{ \n";
		$config.= "\t ipaddr = ".$this['ipaddr']."\n";
		$config.= "\t secret = ".$this['secret']."\n";
		$config.= "\t coa_server = coa_".$this['name']."\n";
		$config.= "}\n";

		$config .= "home_server  coa_".$this['name']." {\n";
		$config .= "\t type = coa \n";
		$config .= "\t ipaddr = ".$this['ipaddr']."\n";
		$config .= "\t secret = ".$this['secret']."\n";
		$config .= "\t port = 3799 \n";
		$config .= "\tcoa { \n".
						"\t\t irt = 2"." \n".
						"\t\tmrt = 16"." \n".
						"\t\tmrc = 5"." \n".
						"\t\tmrd = 30"." \n".
					"\t\t} \n".
				"\t}";

		return $config;
	}
}