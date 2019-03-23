<?php

namespace xavoc\ispmanager;
class page_correction_macbind extends \xepan\base\Page {

	public $title ="Mac Bind";

	function init(){
		parent::init();

		$grid = $this->add('Grid');
		$controller = $this->add('xavoc\ispmanager\Controller_AutoMacBind');
		$controller->setGrid($grid);
		$controller->run(null,null,null,true);
		
	}
}