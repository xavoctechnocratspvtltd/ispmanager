<?php

namespace xavoc\ispmanager;

class Tool_Staff_Panel extends \xepan\cms\View_Tool{
	function init(){
		parent::init();

		if(!$this->app->auth->isLoggedIn()){
			$this->app->redirect($this->app->url('staff_login'));
			return;
		}
			
		$staff = $this->app->employee;

		$col = $this->add('Columns')->addClass('row');
		$col1 = $col->addColumn(4)->addClass('col-md-4 col-lg-4 col-sm-12 col-xs-12');
		$col2 = $col->addColumn(4)->addClass('col-md-4 col-lg-4 col-sm-12 col-xs-12');
		$col3 = $col->addColumn(4)->addClass('col-md-4 col-lg-4 col-sm-12 col-xs-12');

		$open_lead = $this->add('xavoc\ispmanager\Model_Lead');
		$open_lead->addCondition('status','Open');
		$open_lead->addCondition('assign_to_id',$staff->id);
		
		$open_lead_content = '<a href="?page=staff_lead">'.$open_lead->count()->getOne().'</a>';
		
		$col1->add('xepan\epanservices\View_Panel',
						[	
							'theme_class'=>'panel-info',
							'heading'=>'Open Assign Leads',
							'content'=>$open_lead_content,
							'footer'=>''
						]);

		$col2->add('xepan\epanservices\View_Panel',
						[
							'heading'=>'Pending Task',
							'content'=>'000',
							'footer'=>''
						]);

		$col3->add('xepan\epanservices\View_Panel',
						[
							'heading'=>'Total Registration Done by you',
							'content'=>'0000',
							'footer'=>''
						]);
		
	}
}