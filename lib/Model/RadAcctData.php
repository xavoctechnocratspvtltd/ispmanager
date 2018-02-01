<?php

namespace xavoc\ispmanager;

class Model_RadAcctData extends \xavoc\ispmanager\Model_RadAcct{ 

	function init(){
		parent::init();

		$this->addExpression('date')->set($this->dsql()->expr(' DATE_FORMAT(acctstarttime,"%d-%b-%Y")'));
		$this->addExpression('month_year')->set($this->dsql()->expr(' DATE_FORMAT(acctstarttime,"%b-%Y")'));
		$this->addExpression('year')->set($this->dsql()->expr(' DATE_FORMAT(acctstarttime,"%Y")'));
		$this->addExpression('day')->set($this->dsql()->expr(' DATE_FORMAT(acctstarttime,"%d")'));

		$this->addExpression('total_download')->set('sum(acctoutputoctets)')->type('int');
		$this->addExpression('total_upload')->set('sum(acctinputoctets)')->type('int');
		$this->addExpression('total_data')->set(function($m,$q){
			return $q->expr('sum([0]+[1])',[$m->fieldQuery('total_upload'),$m->fieldQuery('total_download')]);
		});

	}
}