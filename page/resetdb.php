<?php

namespace xavoc\ispmanager;
class page_resetdb extends \xepan\base\Page {
	public $title ="Reset DB";

	function page_index(){
		// parent::init();
		
		// if($_GET['reset']){
			$vp = $this->add('VirtualPage');
			$vp->set([$this,'resetdb']);
		// }

		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->showLables(true)
			->makePanelsCoppalsible(true)
			->layout([
					'contact_created_at'=>'c1~4',
					'delete_all_isp_user'=>'c2~4',
					'delete_all_invoice'=>'c3~4',
					'delete_all_order'=>'c4~4',
					'delete_all_quotation'=>'c5~4',
					'delete_all_stock'=>'c7~4',
					'delete_all_support_ticket'=>'c8~4',
					'delete_all_caf_data'=>'c9~4',
				]);

		$form->addField('DatePicker','contact_created_at');
		$form->addField('checkbox','delete_all_isp_user');
		$form->addField('checkbox','delete_all_invoice');
		$form->addField('checkbox','delete_all_order');
		$form->addField('checkbox','delete_all_quotation');
		$form->addField('checkbox','delete_all_stock');
		$form->addField('checkbox','delete_all_support_ticket');
		$form->addField('checkbox','delete_all_caf_data');
		$form->addSubmit('Reset DB Now');
		if($form->isSubmitted()){
			$form->js()->univ()->frameURL('Resting DB',$this->app->url($vp->getURL(),[
					'contact_created_at'=>$form['contact_created_at'],
					'delete_all_isp_user'=>$form['delete_all_isp_user'],
					'delete_all_invoice'=>$form['delete_all_invoice'],
					'delete_all_order'=>$form['delete_all_order'],
					'delete_all_quotation'=>$form['delete_all_quotation'],
					'delete_all_stock'=>$form['delete_all_stock'],
					'delete_all_support_ticket'=>$form['delete_all_support_ticket'],
					'delete_all_caf_data'=>$form['delete_all_caf_data']]))->execute();
		}
	}

	function resetdb($page){
		$this->app->stickyGET('contact_created_at');
		$this->app->stickyGET('delete_all_isp_user');
		$this->app->stickyGET('delete_all_invoice');
		$this->app->stickyGET('delete_all_order');
		$this->app->stickyGET('delete_all_quotation');
		$this->app->stickyGET('delete_all_stock');
		$this->app->stickyGET('delete_all_support_ticket');
		$this->app->stickyGET('delete_all_caf_data');

		$page->add('View_Console')->set(function($c){
			$c->out('--------*** Reset Started ***--------');
		});

	}
}