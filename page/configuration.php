<?php

namespace xavoc\ispmanager;


class page_configuration extends \xepan\base\Page {
	
	public $title ="Configuration";

	function init(){
		parent::init();


		$tab = $this->add('Tabs');
		$location = $tab->addTab('Location');
		$l_tab = $location->add('Tabs');
		$c_tab  = $l_tab->addTab('Country');
		$s_tab = $l_tab->addTab('State');
		$city_tab = $l_tab->addTab('City');

		$crud = $c_tab->add('xepan\hr\CRUD');
		$crud->setModel('xavoc\ispmanager\Country');

		$crud = $s_tab->add('xepan\hr\CRUD');
		$crud->setModel('xavoc\ispmanager\State');

		$crud = $city_tab->add('xepan\hr\CRUD');
		$crud->setModel('xavoc\ispmanager\City');
	}
}