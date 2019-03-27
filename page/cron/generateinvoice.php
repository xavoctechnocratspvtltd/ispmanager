<?php

namespace xavoc\ispmanager;

class page_cron_generateinvoice extends \xepan\base\Page{

	function init(){
		parent::init();

		ini_set("memory_limit", "-1");
   		set_time_limit(0);

		$this->add('xavoc\ispmanager\Controller_GenerateInvoice')->run();
	}

}