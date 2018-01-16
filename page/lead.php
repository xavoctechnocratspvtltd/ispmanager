<?php


namespace xavoc\ispmanager;

class page_lead extends \xepan\marketing\page_lead{
	public $title = "Lead Management";
	public $model_class = "xavoc\ispmanager\Model_Lead";


	function page_index(){
		parent::page_index();
		
		$model = $this->add('xavoc\ispmanager\Model_Lead');
		$data = $this->app->db->dsql()->expr('SELECT DISTINCT(city) AS city FROM contact')->get();
		$city_list = [];
		foreach ($data as $key => $value) {
			if(!trim($value['city'])) continue;
			$city_list[$value['city']] = $value['city'];
		}

		$city_field = $this->filter_form->addField('DropDown','filter_city');
		$city_field->setValueList($city_list);
		$city_field->setEmptyText('Select City to filter');

		$this->filter_form->addHook('applyFilter',function($f,$m){
			if($f['filter_city']){
				$m->addCondition('city',$f['filter_city']);
			}
		});
		$city_field->js('change',$this->filter_form->js()->submit());

	}

}