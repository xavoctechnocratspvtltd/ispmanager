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
				if($i == 16) $i = 20;
				if($i == 22) $i = 23;
				if($i == 24) $i = 26;
				if($i == 24) $i = 26;
				if($i == 131) $i = 139;

				$last_inv = $model['document_no'];
				$model['document_no'] = $i;
				$model->save();

				$this->add('View')->set('Last Invoice No: '.$last_inv." - New Invoice No: ".$model['document_no']." Invoice Date: ".$model['created_at']);
				$i++;
			}
		}
	}
}