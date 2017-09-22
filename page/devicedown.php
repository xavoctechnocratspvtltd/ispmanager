<?php

namespace xavoc\ispmanager;


class page_devicedown extends \xepan\base\Page {
	
	public $title ="Device Down Information";

	function init(){
		parent::init();

		$failed_device_id = $_GET['failed_device_id'];

		$device = $this->add('xavoc\ispmanager\Model_Device')->tryLoad($failed_device_id?:0);
		$device->markDown();

	}
}