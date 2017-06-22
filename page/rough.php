<?php


namespace xavoc\ispmanager;

class page_rough extends \xepan\base\Page {
	
	function init(){
		parent::init();

		$accounting_data=['10gb',0];

		$q = $this->app->db->dsql();
		$q->table('isp_user_plan_and_topup')
				->set('download_data_consumed',$q->expr('download_data_consumed + [0]',[$this->app->human2byte($accounting_data[0])]))
				->set('upload_data_consumed',$q->expr('upload_data_consumed + [0]',[$this->app->human2byte($accounting_data[1])]))
				->where('is_effective',1)
				->where('user_id',142443)
				->debug()
				->update()
				;

	}
}