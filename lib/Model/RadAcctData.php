<?php

namespace xavoc\ispmanager;

class Model_RadAcctData extends \xavoc\ispmanager\Model_RadAcct{ 

	function init(){
		parent::init();

		$this->addExpression('date')->set($this->dsql()->expr(' DATE_FORMAT(acctstarttime,"%d-%b-%Y")'));
		$this->addExpression('month_year')->set($this->dsql()->expr(' DATE_FORMAT(acctstarttime,"%b-%Y")'));
		$this->addExpression('year')->set($this->dsql()->expr(' DATE_FORMAT(acctstarttime,"%Y")'));
		$this->addExpression('day')->set($this->dsql()->expr(' DATE_FORMAT(acctstarttime,"%d")'));

		$this->addExpression('total_download')->set('sum(acctoutputoctets)');
		$this->addExpression('total_upload')->set('sum(acctinputoctets)');
		// $this->addExpression('total_data_consumed')->set(function($m,$q){
		// 	return $q->expr('[0]+[1]',[$m->fieldQuery('total_download'),$m->fieldQuery('total_upload')]);
		// });

		$this->addExpression('duration_in_sec')->set(function($m,$q){
			return $q->expr("(TIMESTAMPDIFF(SECOND,[0], IFNULL([1],'[2]')))",[$m->getElement('acctstarttime'),$m->getElement('acctstoptime'),$m->getElement('acctupdatetime')]);
		});

		$this->addExpression('duration_in_hms')->set(function($m,$q){
			return $q->expr("SEC_TO_TIME([0])",[$m->getElement('duration_in_sec')]);
		});

		$this->addExpression('total_duration_in_sec')->set(function($m,$q){
			return $q->expr("sum((TIMESTAMPDIFF(SECOND,[0], IFNULL([1],'[2]'))))",[$m->getElement('acctstarttime'),$m->getElement('acctstoptime'),$m->getElement('acctupdatetime')]);
		});

		$this->addExpression('total_duration_in_hms')->set(function($m,$q){
			return $q->expr("SEC_TO_TIME([0])",[$m->getElement('total_duration_in_sec')]);
		});

	}
}