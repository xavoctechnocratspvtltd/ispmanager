<?php

namespace xavoc\ispmanager;


/**
* 
*/
class Tool_CurrentPlan extends \xepan\cms\View_Tool{
	public $options = [
						'show_data'=>'boolean',
						'show_validity'=>'boolean',
						'show_bandwith'=>'boolean',
						'show_fup'=>'boolean'
	];

	function init(){
		parent::init();
		if($this->owner instanceof \AbstractController) return;
		
		$this->add('View')->setHTML(" 
			<h3>Current Plan</h3>
			<p>Data Limit: 50GB</p>
			<p>Remaning Data: 20GB</p>
			<p>Valid: For 28 days</p>
			<p>Remainig Days: 5 days</p>
			<p>BandWidth: 2mbps</p>
			<p>FUP: 512kbps</p>
			");
	}

}