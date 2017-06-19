<?php

namespace xavoc\ispmanager;

class Tool_StaffMenuBar extends \xepan\cms\View_Tool{
	function init(){
		parent::init();

		$this->add('View');
	}

	// function defaultTemplate(){
	// 	return ['xavoc/tool/staffmenubar'];
	// }
}