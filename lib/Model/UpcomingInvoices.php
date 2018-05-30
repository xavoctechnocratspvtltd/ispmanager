<?php

namespace xavoc\ispmanager;

class Model_UpcomingInvoices  extends Model_UserPlanAndTopup{
	public $to_date; //used for date diff calculation

	function init(){
		parent::init();

		if(!$this->to_date) $this->to_date = $this->app->today;
						
		$this->addExpression('radius_username')->set($this->refSQL("user_id")->fieldQuery('radius_username'))->sortable(true);
		$this->addExpression('sale_price')->set($this->refSQL('plan_id')->fieldQuery('sale_price'));
		$this->addExpression('user_status')->set($this->refSQL('user_id')->fieldQuery('status'));
		
		// date diff expression
		$this->addExpression('days_count')->set(function($m,$q){
			return $q->expr('TIMESTAMPDIFF(DAY,[end_date],[to_date])',['end_date'=>$m->getElement('end_date'),'to_date'=>'"'.$this->to_date.'"']);
		})->type('number');

		$this->addExpression('weeks_count')->set(function($m,$q){
			return $q->expr('TIMESTAMPDIFF(WEEK,[end_date],[to_date])',['end_date'=>$m->getElement('end_date'),'to_date'=>'"'.$this->to_date.'"']);
		})->type('number');

		$this->addExpression('months_count')->set(function($m,$q){
			return $q->expr('TIMESTAMPDIFF(MONTH,[end_date],[to_date])',['end_date'=>$m->getElement('end_date'),'to_date'=>'"'.$this->to_date.'"']);
		})->type('number');

		$this->addExpression('years_count')->set(function($m,$q){
			return $q->expr('TIMESTAMPDIFF(YEAR,[end_date],[to_date])',['end_date'=>$m->getElement('end_date'),'to_date'=>'"'.$this->to_date.'"']);
		})->type('number');

		$this->addExpression('plan_renewable_value')->set($this->refSQL('plan_id')->fieldQuery('renewable_value'))->type('number');
		$this->addExpression('plan_renewable_unit')->set($this->refSQL('plan_id')->fieldQuery('renewable_unit'));

		$this->addExpression('total_upcoming_invoice')->set(function($m,$q){
			return $q->expr('FLOOR((
				CASE [punit]
				WHEN "DAYS" THEN [days_count]
				WHEN "WEEKS" THEN [weeks_count]
				WHEN "MONTHS" THEN [months_count]
				WHEN "YEARS" THEN [years_count]
				END
			)/[pvalue])',
									[
										'pvalue'=>$m->getElement('plan_renewable_value'),
										'punit'=>$m->getElement('plan_renewable_unit'),
										'months_count'=>$m->getElement('months_count'),
										'days_count'=>$m->getElement('days_count'),
										'weeks_count'=>$m->getElement('weeks_count'),
										'years_count'=>$m->getElement('years_count')
									]);
		})->sortable(true);

		$this->addExpression('upcoming_invoice_amount')->set(function($m,$q){
			return $q->expr('([0]*[1])',[$m->getElement('sale_price'),$m->getElement('total_upcoming_invoice')]);
		})->type('number')->sortable(true);

	}
}