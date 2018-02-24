<?php

namespace xavoc\ispmanager;

class page_channel_agent extends \xavoc\ispmanager\page_channel_channel {
	
	public $title = "Agent Management";
	public $model_class = "xavoc\ispmanager\Model_Agent";
	
	function init(){
		parent::init();
		
	}
}