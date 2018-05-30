<?php

namespace xavoc\ispmanager;

/**
* 
*/
class page_auditlog extends \xepan\base\Page{
	public $title = "Audit Log ";
	function init(){
		parent::init();

		$filter = $this->app->stickyGET('filter');
		$employee_id = $this->app->stickyGET('employee_id');
		$from_date = $this->app->stickyGET('from_date');
		$to_date = $this->app->stickyGET('to_date');
		$looking_for = $this->app->stickyGET('looking_for');
		$search_string = $this->app->stickyGET('search_string');

		$skip_page = $this->app->stickyGET('pagintor');

		$f = $this->add('Form');
		$f->add('xepan\base\Controller_FLC')
			->addContentSpot()
			->layout([
					'contact~Employee'=>'Filter~c1~2',
					'looking_for'=>'c2~2',
					'search_string'=>'s1~2',
					'from_date'=>'c3~2',
					'to_date'=>'c4~2',
					'FormButtons~'=>'c5~2',
				]);

		$contact_model = $this->add('xepan\hr\Model_Employee');
		$contact_field = $f->addField('xepan\base\Basic','contact_id');
		$contact_field->setModel($contact_model);
		$contact_field->set($employee_id);

		$distinct_model_class = $this->app->db->dsql()->expr('SELECT DISTINCT(model_class) AS model_class FROM xepan_auditlog')->get();
		$distinct_model_class = array_column($distinct_model_class, "model_class");
		$distinct_model_class = array_combine($distinct_model_class,$distinct_model_class);

		$f->addField('DateTimePicker','from_date')->set($from_date?:$this->app->now);
		$f->addField('DateTimePicker','to_date')->set($to_date?:$this->app->now);
		$f->addField('DropDown','looking_for')->setValueList($distinct_model_class)->setEmptyText('Any');
		$f->addField('Line','search_string');

		$f->addSubmit('Get Detail')->addClass('btn btn-primary');

		$grid_view = $this->add('View');
		$log = $this->add('xepan\base\Model_AuditLog');

		if($filter){
			if($employee_id)
				$log->addCondition('contact_id',$contact_id);
			if($from_date)
				$log->addCondition('created_at','>=',$from_date);
			if($to_date)
				$log->addCondition('created_at','<',$this->app->nextDate($to_date));
			if($search_string)
				$log->addCondition('name','like','%'.$search_string.'%');
			if($looking_for)
				$log->addCondition('model_class',$looking_for);	
		}else{
			$log->addCondition('id',-1);
		}
		$log->setOrder('created_at','desc');
		$log->getElement('contact_id')->caption('Employee');
		$grid = $grid_view->add('xepan\base\Grid');
		$grid->setModel($log);
		$grid->addPaginator(50);

		if($f->isSubmitted()){
			$grid_view->js()->reload([
					'filter'=>1,
					'from_date'=>$f['from_date']?:0,
					'to_date'=>$f['to_date']?:0,
					'looking_for'=>$f['looking_for'],
					'search_string'=>$f['search_string'],
					'employee_id'=>$f['contact_id'],
				])->execute();
		}

		$grid->addHook('formatRow',function($g){
			$temp = explode("_", $g->model['model_class']);
			$g->current_row_html['model_class'] = end($temp);

			$g->current_row_html['name'] = $g->model['name'];

		});
		$grid->removeColumn('user');
		$grid->removeColumn('name');
	}
}