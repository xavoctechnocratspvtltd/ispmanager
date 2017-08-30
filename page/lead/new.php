<?php


namespace xavoc\ispmanager;

class page_lead_new extends \xepan\marketing\page_lead{
	public $title = "Create New Lead";

	function init(){

		$lead_model = $this->add('xavoc\ispmanager\Model_Lead');

		$form = $this->add('Form');
		$form->setModel($lead_model);
	}
}