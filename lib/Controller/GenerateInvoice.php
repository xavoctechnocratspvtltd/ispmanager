<?php

namespace xavoc\ispmanager;

class Controller_GenerateInvoice extends \AbstractController {

	function run($from_date=null,$to_date=null,$branch_id=null){

		$from_date = $from_date?:$this->app->today;
		$to_date = $to_date?:$this->app->today;

		$model = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
		$model->addExpression('last_invoice_date')->set(function($m,$q){
			$act = $m->add('xavoc\ispmanager\Model_Invoice')
					->addCondition('contact_id',$m->getElement('user_id'))
					->setOrder('id','desc')
					->setLimit(1);
			return $q->expr('IFNULL(DATE([0]),0)',[$act->fieldQuery('created_at')]);
		});
		$model->addExpression('user_status')->set(function($m,$q){
			return $m->refSQL("user_id")->fieldQuery('status');
		});

		if($to_date)
			$model->addCondition('end_date','<=',$to_date);
		if($from_date)
			$model->addCondition('end_date','>=',$from_date);
		if($branch_id)
			$model->addCondition('branch_id',$branch_id);

		$model->addCondition('end_date','<>',$model->getElement('last_invoice_date'));

		$model->addCondition('user_status','Active');
		$model->_dsql()->where('id in ( select max(id) from isp_user_plan_and_topup group by user_id)');
		$model->setActualFields(['id','last_invoice_date','end_date','user_status']);
		
		foreach ($model as $data) {
			if(strtotime($data['end_date']) == strtotime(date('Y-m-d',strtotime($data['last_invoice_date'])))){
          		continue;
          	}

          	try{
          		$this->app->db->beginTransaction();
          		// echo "<pre>";
          		// print_r($data->data);
          		// echo "</pre>";
          		$data->createInvoice('Submitted');
          		$this->app->db->commit();
          	}catch(\Exception $e){
          		$this->app->db->rollback();
          	}
		}

	}

}