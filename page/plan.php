<?php

namespace xavoc\ispmanager;


class page_plan extends \xepan\base\Page {
	
	public $title ="Plan";

	function page_index(){
		// parent::init();

		$plan = $this->add('xavoc\ispmanager\Model_BasicPlan');
		$plan->addExpression('validity')->set(function($m,$q){
			return $q->expr('CONCAT([0]," ",[1])',[$m->getElement('plan_validity_value'),$m->getElement('qty_unit')]);
		});

		$crud = $this->add('xepan\hr\CRUD');
		if($crud->isEditing()){
			$form = $crud->form;
			$form->setLayout('form/plan');
		}
		$crud->setModel($plan,['name','sku','description','sale_price','original_price','status','document_id','id','created_by','updated_by','created_at','updated_at','type','qty_unit_id','qty_unit','renewable_unit','renewable_value','tax_id','tax','plan_validity_value','is_auto_renew','available_in_user_control_panel'],['name','code','sale_price','validity']);
		$crud->grid->removeColumn('attachment_icon');
		$crud->grid->addQuickSearch(['name']);
		// $crud->grid->addOrder()->move('qty_unit','after','plan_validity_value')->now();
		
		$grid = $crud->grid;
		$import_btn = $grid->addButton('Import CSV')->addClass('btn btn-primary');
		$import_btn->setIcon('fa fa fa-arrow-up');

		$import_btn->js('click')
			->univ()
			->frameURL(
					'Import CSV',
					$this->app->url('./import')
					);		
	}

	function page_import(){

		$col = $this->add('Columns');
		$col1 = $col->addColumn('6')->addClass('col-md-6 col-lg-6 col-sm-12');
		$col2 = $col->addColumn('6')->addClass('col-md-6 col-lg-6 col-sm-12');

		$form = $col1->add('Form');
		$form->addSubmit('Download Sample File')->addClass('btn btn-primary');
		
		if($_GET['download_sample_csv_file']){
			$output = ['NAME','CODE','STATUS','ORIGINAL_PRICE','SALE_PRICE','TAX','PLAN_VALIDITY_VALUE','PLAN_VALIDITY_UNIT','DESCRIPTION','RENEWABLE_VALUE','RENEWABLE_UNIT','IS_AUTO_RENEW','AVAILABLE_IN_USER_CONTROL_PANEL','REMARK','DATA_LIMIT','DOWNLOAD_LIMIT','UPLOAD_LIMIT','FUP_DOWNLOAD_LIMIT','FUP_UPLOAD_LIMIT','ACCOUNTING_DOWNLOAD_RATIO','ACCOUNTING_UPLOAD_RATIO','IS_DATA_CARRY_FORWARD','START_TIME','END_TIME','SUN','MON','TUE','WED','THU','FRI','SAT','D01','D02','D03','D04','D05','D06','D07','D08','D09','D10','D11','D12','D13','D14','D15','D16','D17','D18','D19','D20','D21','D22','D23','D24','D25','D26','D27','D28','D29','D30','D31','DATA_RESET_VALUE','DATA_RESET_MODE','TREAT_FUP_AS_DL_FOR_LAST_LIMIT_ROW','IS_PRO_DATA_AFFECTED','BURST_DL_LIMIT','BURST_UL_LIMIT','BURST_THRESHOLD_DL_LIMIT','BURST_THRESHOLD_UL_LIMIT','BURST_DL_TIME','BURST_UL_TIME','PRIORITY'];
			$output = implode(",", $output);
	    	header("Content-type: text/csv");
	        header("Content-disposition: attachment; filename=\"sample_xepan_plan_import.csv\"");
	        header("Content-Length: " . strlen($output));
	        header("Content-Transfer-Encoding: binary");
	        print $output;
	        exit;
		}

		if($form->isSubmitted()){
			$form->js()->univ()->newWindow($form->app->url('xavoc_ispmanager_plan_import',['download_sample_csv_file'=>true]))->execute();
		}

		$form_delete = $col2->add('Form');
		$form_delete->addSubmit('Delete All Plan Forcely')->addClass('btn btn-danger');
		if($form_delete->isSubmitted()){
	        
	        $plans = $this->add('xavoc\ispmanager\Model_Plan');
	        foreach ($plans as $model) {
	        	$model->delete();
	        }
			$this->add('xavoc\ispmanager\Model_Condition')->deleteAll();
			$this->add('xavoc\ispmanager\Model_UserPlanAndTopup')->deleteAll();

			// $users = $this->add('xavoc\ispmanager\Model_User')->addCondition('plan_id','>',0);
			// $users->addHook('beforeDelete',function($m){
			// 	throw new \Exception("Error Processing Request", 1);
			// });

			$form_delete->js()->univ()->successMessage("Plan's Deleted Successfully")->execute();
		}

		$this->add('View')->setElement('iframe')->setAttr('src',$this->api->url('./execute',array('cut_page'=>1)))->setAttr('width','100%');
		
		$this->add('View',null,null,['view/planscvdetail']);
	}

	function page_import_execute(){

		ini_set('max_execution_time', 0);

		$form= $this->add('Form');
		$form->template->loadTemplateFromString("<form method='POST' action='".$this->api->url(null,array('cut_page'=>1))."' enctype='multipart/form-data'>
			<input type='file' name='csv_plan_file'/>
			<input type='submit' value='Upload'/>
			</form>"
			);

		if($_FILES['csv_plan_file']){
			if ( $_FILES["csv_plan_file"]["error"] > 0 ) {
				$this->add( 'View_Error' )->set( "Error: " . $_FILES["csv_plan_file"]["error"] );
			}else{
				$mimes = ['text/comma-separated-values', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.ms-excel', 'application/vnd.msexcel', 'text/anytext'];
				if(!in_array($_FILES['csv_plan_file']['type'],$mimes)){
					$this->add('View_Error')->set('Only CSV Files allowed');
					return;
				}

				$importer = new \xepan\base\CSVImporter($_FILES['csv_plan_file']['tmp_name'],true,',');
				$data = $importer->get();

				$plan = $this->add('xavoc\ispmanager\Model_Plan');
				$plan->import($data);
				$this->add('View_Info')->set('Total Records : '.count($data));
			}
		}
	
	}
}