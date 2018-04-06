<?php


namespace xavoc\ispmanager;

class page_lead_installationassigned extends \xepan\base\Page{
	public $title = "Lead Assigned for installation";

	function init(){
		parent::init();

		$model = $this->add('xavoc\ispmanager\Model_User');
		$model->addCondition('status','Installation');
		
		$crud = $this->add('xepan\hr\CRUD',['allow_add'=>false]);
		$crud->setModel($model,['name','plan','organization','installation_assign_at','installation_assign_to','installed_narration','shipping_address','shipping_city','shipping_state','remark','created_at','contacts_str','emails_str','status','created_by']);

		$grid = $crud->grid;

		$grid->addHook('formatRow',function($g){
			$g->current_row_html['shipping_address'] = $g->model['shipping_address']."<br/>".$g->model['shipping_city']."<br/>".$g->model['shipping_state'];
			$g->current_row_html['name'] = $g->model['name']."<br/>( ".$g->model['organization']." )<br/>".$g->model['contacts_str']."<br/>".$g->model['emails_str'];
			$g->current_row_html['created_at'] = $g->model['created_at']."<br/>( ".$g->model['created_by']." )";
			$g->current_row_html['installation_assign_at'] = $g->model['installation_assign_at']."<br/>( ".$g->model['installation_assign_to']." )";
			$g->current_row_html['remark'] = $g->model['remark']."<br/>( ".$g->model['installed_narration']." )";
		});

		$grid->removeColumn('shipping_state');
		$grid->removeColumn('installation_assign_to');
		$grid->removeColumn('shipping_city');
		$grid->removeColumn('created_by');
		$grid->removeColumn('organization');
		$grid->removeColumn('status');
		$grid->removeColumn('contacts_str');
		$grid->removeColumn('emails_str');
		$grid->removeColumn('installed_narration');
		$grid->removeAttachment();

		$grid->addQuickSearch(['name','contacts_str','emails_str','shipping_address','shipping_city']);
		$grid->addPaginator(25);

	}
}