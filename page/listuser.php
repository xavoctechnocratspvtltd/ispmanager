<?php

namespace xavoc\ispmanager;

class page_listuser extends \xavoc\ispmanager\page_user {	
	public $title = "User List";
	public $datastatus = false;
	public $model_class = "xavoc\ispmanager\Model_User";
	public $paginator = 10;

}