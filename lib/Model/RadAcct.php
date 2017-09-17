<?php

namespace xavoc\ispmanager;

class Model_RadAcct extends \xepan\base\Model_Table{ 
	public $table = "radacct";
	public $id_field = "radacctid";

	function init(){
		parent::init();

		$this->addField('acctsessionid');
		$this->addField('acctuniqueid');
		$this->addField('username');
		$this->addField('groupname');
		$this->addField('realm');
		$this->addField('nasipaddress');
		$this->addField('nasportid');
		$this->addField('nasporttype');
		$this->addField('acctstarttime');
		$this->addField('acctupdatetime');
		$this->addField('acctstoptime');
		$this->addField('acctinterval');
		$this->addField('acctsessiontime');
		$this->addField('acctauthentic');
		$this->addField('connectinfo_start');
		$this->addField('connectinfo_stop');
		$this->addField('acctinputoctets');
		$this->addField('acctoutputoctets');
		$this->addField('calledstationid');
		$this->addField('callingstationid');
		$this->addField('acctterminatecause');
		$this->addField('servicetype');
		$this->addField('framedprotocol');
		$this->addField('framedipaddress');
	}
}