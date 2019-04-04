<?php

namespace xavoc\ispmanager;

class Controller_AutoMacBind extends \AbstractController {
	public $grid = null;

	function run($from_date=null,$to_date=null,$test_user=null,$debug=false){

		$user_model = $this->add('xavoc\ispmanager\Model_User');
		$user_model->addExpression('recent_mac_address')->set(function($m,$q){
			$radacct_m = $m->add('xavoc\ispmanager\Model_RadAcct');
			$radacct_m->addCondition('username',$m->getElement('radius_username'));
			$radacct_m->addCondition('callingstationid','<>',null);
			$radacct_m->setOrder('radacctid','desc');
			$radacct_m->setLimit(1);
			return $q->expr('[0]',[$radacct_m->fieldQuery('callingstationid')]);
		});

		// not for hotspot user
		$user_model->addCondition([['is_hotspotuser',false],['is_hotspotuser',null]]);
		$user_model->addCondition('mac_address',null);
		$user_model->addCondition('radius_username','<>',null);
		// $user_model->addCondition('first_name','<>',null);
		
		$user_data_row = $user_model->getRows();
		
		if(!$debug){
			try{
				foreach($user_data_row as $model){

					$radcheck = $this->add('xavoc\ispmanager\Model_RadCheck');
					$radcheck->addCondition('value',$model['recent_mac_address']);
					if($radcheck->count()->getOne() > 1){ continue; }

					$radcheck->tryLoadAny();
					if($radcheck->loaded() AND $radcheck['username'] != $model['radius_username']) continue;

					$radcheck['username'] = $model['radius_username'];
					$radcheck['attribute'] = "Calling-Station-Id";
					$radcheck['op'] = ":=";
					$radcheck->saveAndUnload();

					$query = "UPDATE `isp_user` SET `mac_address` = '".$model['recent_mac_address']."' WHERE `isp_user`.`customer_id` = ".$model['id'].";";
					// $model['mac_address'] = $model['recent_mac_address'];
					// $model->save();
					$this->app->db->dsql()->expr($query)->execute();
				}
			}catch(\Exception $e){

			}
		}

		if($debug && $this->grid)
			$this->grid->setModel($user_model,['radius_username','unique_name','plan','mac_address','recent_mac_address']);
	}

	function setGrid($grid){
		$this->grid = $grid;
	}

}