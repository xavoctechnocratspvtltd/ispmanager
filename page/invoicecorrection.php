<?php

namespace xavoc\ispmanager;

class page_invoicecorrection extends \xepan\base\Page {
	public $title = "Invoice Correction";
	function init(){
		parent::init();

		$model = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
		$model->addExpression('last_invoice_date')->set(function($m,$q){
			$act = $m->add('xavoc\ispmanager\Model_Invoice')
					->addCondition('contact_id',$m->getElement('user_id'))
					->setOrder('id','desc')
					->setLimit(1);
			return $q->expr('IFNULL([0],0)',[$act->fieldQuery('created_at')]);
		});
			
		$model->addExpression('radius_username')->set($model->refSql('user_id')->fieldQuery('radius_username'));
		$crud = $this->add('CRUD',['allow_add'=>false]);
		$crud->setModel($model,['user','radius_username','plan','end_date','download_data_consumed','upload_data_consumed','last_invoice_date']);
		$crud->grid->addPaginator(20);
		$crud->grid->addQuickSearch(['user','end_date','radius_username']);

		$crud->grid->addHook('formatRow',function($g){
			if($g->model['end_date'] == $g->model['last_invoice_date'])
				$g->current_row_html['last_invoice_date'] = "<div class='alert alert-success'>Yes, Invoice created <br/><strong>".$g->model['last_invoice_date']."</strong></div>";
			else
				$g->current_row_html['last_invoice_date'] = "<div class='alert alert-danger'>No, Last Invoice Date: <br/><strong>".$g->model['last_invoice_date']."</strong></div>";
		});
	}	
}