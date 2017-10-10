<?php


namespace xavoc\ispmanager;

/**
	
	Model on owner grid must have following expresion defined 

	$model->addExpression('radius_login_response')->set(function($m,$q){
		return $q->expr('(select checkAuthentication(null,"[0]"))',[$m->getElement('radius_username')]);
	})->caption('Data Status');

	AND

	grid/crud fields in setModel must have radius_login_response added

*/

class Controller_Grid_Format_userDataStatus extends \Controller_Grid_Format {
	/**
     * Initialize field
     *
     * Note: $this->owner is Grid object
     * 
     * @param string $name Field name
     * @param string $descr Field title
     *
     * @return void
     */
    public function initField($name, $descr) {
    	// $this->owner->model->addExpression('data1')->set('"123"');
    }
    
    /**
     * Format output of cell in particular row
     *
     * Note: $this->owner is Grid object
     * 
     * @param string $field Field name
     * @param array $column Array [type=>string, descr=>string]
     *
     * @return void
     */
    public function formatField($field, $column) {
    	// $this->owner->current_row[$field]=$this->owner->model['data1'];
    }
}