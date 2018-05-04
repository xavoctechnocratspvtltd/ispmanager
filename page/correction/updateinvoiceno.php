<?php

namespace xavoc\ispmanager;

class page_correction_updateinvoiceno extends \xepan\base\Page {

	public $title ="Correction Page";

	function init(){
		parent::init();

		set_time_limit(0);
		$inv = $this->add('xavoc\ispmanager\Model_Invoice');
		$inv->setOrder('created_at','asc');
		if($_GET['do']){
			$i = 1;
			foreach ($inv as $model) {
				
				if($i == 7) $i = 8;
				if($i == 10) $i = 11;
				if($i == 16) $i = 20; // 16 to 19
				if($i == 22) $i = 23; // 21, 22
				if($i == 24) $i = 26; // 24, 25 
				if($i == 27) $i = 28;
				if($i == 39) $i = 40;
				if($i == 43) $i = 44;
				if($i == 48) $i = 49; 
				if($i == 79) $i = 80;
				if($i == 89) $i = 90; 
				if($i == 131) $i = 139; // 131 to 138
				if($i == 380) $i = 382; // 380, 381
				if($i == 359) $i = 361; // 359, 360
				if($i == 421) $i = 431; // 421 to 430

				$last_inv = $model['document_no'];
				$model['document_no'] = $i;
				$model->save();

				$this->add('View')->set('Last Invoice No: '.$last_inv." - New Invoice No: ".$model['document_no']." Invoice Date: ".$model['created_at']);
				$i++;
			}
		}
	}
}