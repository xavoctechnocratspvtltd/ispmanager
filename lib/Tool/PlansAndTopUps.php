<?php

namespace xavoc\ispmanager;


/**
* 
*/
class Tool_PlansAndTopUps extends \xepan\cms\View_Tool{
	public $options = [];

	function init(){
		parent::init();

		if($this->owner instanceof \AbstractController) return;
		$this->add('View')->setHTML(" 
			<h3>Available Plans</h3>
			<p>Data Limit: 100GB</p>
			<p>Valid: For 90 days</p>
			<p>BandWidth: 2mbps</p>
			<p>FUP: 512kbps</p>
			<button type='button'>Buy Now</button> 
			");
	}

}