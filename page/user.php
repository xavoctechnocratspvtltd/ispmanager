<?php

namespace xavoc\ispmanager;

class page_user extends \xepan\base\Page {
	
	public $title ="User";

	function page_index(){
		// parent::init();

		$model = $this->add('xavoc\ispmanager\Model_UserData');
		$model->addHook('afterSave',[$model,'updateUserConditon']);
		$model->addHook('afterSave',[$model,'createInvoice']);
		$model->addHook('afterSave',[$model,'updateNASCredential']);
		$model->addHook('afterSave',[$model,'updateWebsiteUser']);
		$model->setOrder('id','desc');

		

		$crud = $this->add('xepan\hr\CRUD');
		if($crud->isEditing()){
			$form = $crud->form;
			$form->setLayout('form/user');
		}
		$crud->setModel($model,['net_data_limit','radius_username','radius_password','plan_id','simultaneous_use','grace_period_in_days','custom_radius_attributes','first_name','last_name','create_invoice','is_invoice_date_first_to_first','include_pro_data_basis','country_id','state_id','city','address','pin_code','qty_unit_id','mac_address'],['radius_username','created_at','last_login','plan','radius_login_response','is_online']);
		$crud->grid->removeColumn('attachment_icon');
		$crud->grid->addPaginator($ipp=50);
		$crud->grid->addQuickSearch(['radius_username','plan']);
		$crud->grid->addSno(null,true);

		if($crud->isEditing()){
			$form = $crud->form;
			$date_to_date_field = $form->getElement('is_invoice_date_first_to_first');
			$date_to_date_field->js(true)->univ()->bindConditionalShow([
				'1'=>['include_pro_data_basis']
			],'div.atk-form-row');
		}

		$import_btn = $crud->grid->addButton('Import CSV')->addClass('btn btn-primary');
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
			$output = ['RADIUS_USERNAME','RADIUS_PASSWORD','PLAN','SIMULTANEOUS_USE','GRACE_PERIOD_IN_DAYS','FIRST_NAME','LAST_NAME','COUNTRY','STATE','CITY','ADDRESS','PIN_CODE','CREATE_INVOICE','INVOICE_DATE','IS_INVOICE_DATE_FIRST_TO_FIRST','INCLUDE_PRO_DATA_BASIS','CUSTOM_RADIUS_ATTRIBUTES','DATA_CONSUMED','MAC_ADDRESS'];
			$output = implode(",", $output);
	    	header("Content-type: text/csv");
	        header("Content-disposition: attachment; filename=\"sample_xepan_isp_user_import.csv\"");
	        header("Content-Length: " . strlen($output));
	        header("Content-Transfer-Encoding: binary");
	        print $output;
	        exit;
		}

		if($form->isSubmitted()){
			$form->js()->univ()->newWindow($form->app->url('xavoc_ispmanager_user_import',['download_sample_csv_file'=>true]))->execute();
		}

		$form_delete = $col2->add('Form');
		$form_delete->addSubmit('Delete All User Forcely')->addClass('btn btn-danger');
		if($form_delete->isSubmitted()){

			$qsp_master = $this->add('xepan\commerce\Model_QSP_Master');
			foreach ($qsp_master as $qsp) {
				$qsp->delete();
			}

			$users = $this->add('xavoc\ispmanager\Model_User');
			foreach ($users as $user) {
				$user->delete();
			}
			
			foreach ($this->add('xavoc\ispmanager\Model_Condition') as $cond) {
				$cond->delete();
			}

			$this->add('xavoc\ispmanager\Model_RadCheck')->deleteAll();
			$this->add('xavoc\ispmanager\Model_RadPostAuth')->deleteAll();

			$user = $this->add('xepan\base\Model_User');
			$user->addCondition('scope','WebsiteUser');
			$user->deleteAll();

			$ci = $this->add('xepan\base\Model_Contact_Info');
			$ci->addCondition('contact_type','Customer');
			$ci->deleteAll();
			
			$this->app->db->dsql()->expr('DELETE FROM radacct;')->execute();
			$this->app->db->dsql()->expr('DELETE FROM radreply;')->execute();
			$this->app->db->dsql()->expr('DELETE FROM radusergroup;')->execute();

			$form_delete->js()->univ()->successMessage("User's Deleted Successfully")->execute();
		}

		$this->add('View')->setElement('iframe')->setAttr('src',$this->api->url('./execute',array('cut_page'=>1)))->setAttr('width','100%');
		
		$this->add('View')->setHtml('CSV Field Detail: set include_pro_data_basis value in list 1. none 2. invoice_only 3. data_only 4. invoice_and_data_both <br/> Data_Consumed: dl/ul/remark');
	}

	function page_import_execute(){

		ini_set('max_execution_time', 0);

		$form= $this->add('Form');
		$form->template->loadTemplateFromString("<form method='POST' action='".$this->api->url(null,array('cut_page'=>1))."' enctype='multipart/form-data'>
			<input type='file' name='csv_user_file'/>
			<input type='submit' value='Upload'/>
			</form>"
			);

		if($_FILES['csv_user_file']){
			if ( $_FILES["csv_user_file"]["error"] > 0 ) {
				$this->add( 'View_Error' )->set( "Error: " . $_FILES["csv_user_file"]["error"] );
			}else{
				$mimes = ['text/comma-separated-values', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.ms-excel', 'application/vnd.msexcel', 'text/anytext'];
				if(!in_array($_FILES['csv_user_file']['type'],$mimes)){
					$this->add('View_Error')->set('Only CSV Files allowed');
					return;
				}

				$importer = new \xepan\base\CSVImporter($_FILES['csv_user_file']['tmp_name'],true,',');
				$data = $importer->get();

				$user = $this->add('xavoc\ispmanager\Model_User');
				$user->import($data);
				$this->add('View_Info')->set('Total Records : '.count($data));
			}
		}
	}
}