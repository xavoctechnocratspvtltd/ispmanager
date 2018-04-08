<?php

namespace xavoc\ispmanager;

class Model_Config_Mendatory extends \xepan\base\Model_ConfigJsonModel{
	public  $fields	= [
						'customer_type'=>"Line",
						'connection_type'=>"Line",
						'mendatory_fields'=>'Text',
						'mendatory_documents'=>'Text',
						'other_documents'=>'Text',
				];

	public $config_key = 'ISPMANAGER_MENDATORY_MANAGEMENT';
	public $application ='ispmanager';

	function init(){
		parent::init();
	

	}

	function getConnctionTypes(){
		$m = $this->add('xavoc\ispmanager\Model_Config_Mendatory');
		$return_data=[];
		foreach ($m as $t) {
			if($m['connection_type'] !=='') $return_data[$m['connection_type']] = $m['connection_type'];
		}
		return $return_data;
	}

	function getCompanyTypes(){
		$m = $this->add('xavoc\ispmanager\Model_Config_Mendatory');
		$return_data=[];
		foreach ($m as $t) {
			if($m['customer_type'] !=='') $return_data[$m['customer_type']] = $m['customer_type'];
		}
		return $return_data;
	}


	function getFields($customer_or_connection=null,$type=null){
		$m = $this->add('xavoc\ispmanager\Model_Config_Mendatory');

		$m = $this->add('xavoc\ispmanager\Model_Config_Mendatory');
		$return_data=['mendatory_documents'=>[],'other_documents'=>[],'mendatory_fields'=>[]];
		foreach ($m as $d) {
			if($customer_or_connection && $d[$customer_or_connection] == '') continue;
			if($type && $d[$customer_or_connection] != $type) continue;

			$return_data['mendatory_documents'] = array_filter(array_unique(array_merge($return_data['mendatory_documents'],array_map('trim',explode(',', $d['mendatory_documents'])))),function($v){return $v !=='';});
			$return_data['other_documents'] = array_filter(array_unique(array_merge($return_data['other_documents'],array_map('trim',explode(',', $d['other_documents'])))),function($v){return $v !=='';});
			$return_data['mendatory_fields'] = array_filter(array_unique(array_merge($return_data['mendatory_fields'],array_map('trim',explode(',', $d['mendatory_fields'])))),function($v){return $v !=='';});
		}
		$return_data['documents'] = array_unique(array_merge($return_data['mendatory_documents'],$return_data['other_documents']));
		return $return_data;
	}
}