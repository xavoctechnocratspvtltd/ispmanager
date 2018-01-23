<?php

namespace xavoc\ispmanager;

class Model_Channel_User extends \xavoc\ispmanager\Model_User{
	
	function init(){
		parent::init();

		$join = $this->join('isp_channel_association.isp_user_id');
		$join->addField('channel_id');

	}
}