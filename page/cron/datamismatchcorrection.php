<?php

namespace xavoc\ispmanager;

class page_cron_datamismatchcorrection extends \xepan\base\Page{
	
	function init(){
		parent::init();

		$user = $this->add('xavoc\ispmanager\Model_UserUnclosedSession');
		$grid = $this->add('xepan\base\Grid');
		$grid->setModel($user,['radius_username','last_rad_acct_id','unclosed_session_count','condition_expire_date','condition_start_date','reset_mode']);
		
	}
}