<?php

namespace xavoc\ispmanager;

class page_cron_automacbind extends \xepan\base\Page{

	function init(){
		parent::init();

		ini_set("memory_limit", "-1");
   		set_time_limit(0);
   			
   		if($_GET['debug']){
   			$grid = $this->add('Grid');
			$this->add('xavoc\ispmanager\Controller_AutoMacBind',['grid'=>$grid])->run(null,null,null,true);   			
   		}else{
			$this->add('xavoc\ispmanager\Controller_AutoMacBind')->run();
   		}

	}

}