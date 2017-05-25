<?php

namespace xavoc\ispmanager;


class page_runtests extends \xepan\base\Page_TestRunner {
	
	public $title='ISP Manager Tests';
	public $dir='tests';
	public $namespace = __NAMESPACE__;

	function init(){
		if(!set_time_limit(0)) throw new \Exception("Could not limit time", 1);
		parent::init();

		// reset database here

		// empty plan tables
		// run sql files in plan tables
		$this->resetPlan();

		// create a new user named test_user if not exists
		$this->createTestUser();
	}

	function resetPlan(){
		$path = $this->api->pathfinder->base_location->base_path.'/../shared/apps/'.str_replace("\\","/",$this->namespace)."/page/tests";
				
		$plan_sql = $path."/isp_plan.sql";

		if(!file_exists($plan_sql)){
			throw new \Exception("plan sql file not found");
		}

		$query = file_get_contents($plan_sql);
		$this->add('xavoc\ispmanager\Model_Plan')->deleteAll();
		$this->app->db->dsql()->expr('truncate isp_condition')->execute();
		$this->app->db->dsql()->expr('truncate isp_plan')->execute();
		$this->app->db->dsql()->expr('truncate isp_user_plan_and_topup')->execute();
		$this->app->db->dsql()->expr($query)->execute();
	}

	function createTestUser(){
		$user_model = $this->add('xavoc\ispmanager\Model_User')
				->addCondition('name','test user')
				
				;
		if($user_model->count()->getOne() > 1) throw new \Exception("more then one test_user found");
		$user_model->tryLoadAny();
		$user_model['first_name'] = 'test';
		$user_model['last_name'] = 'user';
		$user_model['radius_username'] = 'xavoc';
		$user_model['radius_password'] = 'xavoc';

		$login_user= $this->add('xepan\base\Model_User')
						->addCondition('username','xavoc')
						->addCondition('password','xavoc')
						->tryLoadAny()
						;
		$user_model['user_id'] = $login_user->id;

		$user_model->save();
	}

}
