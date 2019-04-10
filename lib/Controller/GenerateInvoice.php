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
		$model->addExpression('plan_validity_value')->set($model->refSQL('plan_id')->fieldQuery('plan_validity_value'));
		$model->addExpression('qty_unit')->set($model->refSQL('plan_id')->fieldQuery('qty_unit'));

		if($to_date)
			$model->addCondition('end_date','<=',$to_date);
		if($from_date)
			$model->addCondition('end_date','>=',$from_date);
		if($branch_id)
			$model->addCondition('branch_id',$branch_id);

		$model->addCondition('end_date','<>',$model->getElement('last_invoice_date'));
		// $model->addCondition([['is_expired',false],['is_expired',null]]);
		$model->addCondition('user_status','Active');
		$model->_dsql()->where('id in ( select max(id) from isp_user_plan_and_topup group by user_id)');
		$model->setActualFields(['id','last_invoice_date','end_date','user_status','plan_id','plan_validity_value','qty_unit']);
		

		foreach ($model as $data) {
			if(strtotime($data['end_date']) == strtotime(date('Y-m-d',strtotime($data['last_invoice_date'])))){
          		continue;
          	}
			
    //       	if(strtotime(date('Y-m-d',strtotime($data['last_invoice_date']))) >  strtotime("-".$data['plan_validity_value']." ".$data['qty_unit'],strtotime($data['end_date'])) ) // invoice is created between end_date and plan validity in past .. may be for this renew
				// continue;
			
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