<?php

namespace xavoc\ispmanager;


class page_devicedown extends \xepan\base\Page {
	
	public $title ="Device Down Information";

	function init(){
		parent::init();

		$failed_device_id = $_GET['failed_device_id'];

	}
}