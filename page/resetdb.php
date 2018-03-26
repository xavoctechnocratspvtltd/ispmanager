<?php

namespace xavoc\ispmanager;
class page_resetdb extends \xepan\base\Page {
	public $title ="Reset DB";

	function page_index(){
		// parent::init();
		
		ini_set("memory_limit", "-1");
		set_time_limit(0);

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
					// 'delete_all_caf_data'=>'c9~4',
					'delete_all_account_data'=>'c10~4',
					'delete_task'=>'c11~4',
					'delete_rad_post_auth'=>'c12~4',
				]);

		$form->addField('DatePicker','contact_created_at');
		$form->addField('checkbox','delete_all_isp_user');
		$form->addField('checkbox','delete_all_invoice');
		$form->addField('checkbox','delete_all_order');
		$form->addField('checkbox','delete_all_quotation');
		$form->addField('checkbox','delete_all_stock');
		$form->addField('checkbox','delete_all_support_ticket');
		// $form->addField('checkbox','delete_all_caf_data');
		$form->addField('checkbox','delete_all_account_data');
		$form->addField('checkbox','delete_task');
		$form->addField('checkbox','delete_rad_post_auth');


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
					// 'delete_all_caf_data'=>$form['delete_all_caf_data'],
					'delete_all_account_data'=>$form['delete_all_account_data'],
					'delete_task'=>$form['delete_task'],
					'delete_rad_post_auth'=>$form['delete_rad_post_auth']
				]))->execute();
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
		$this->app->stickyGET('delete_all_account_data');
		$this->app->stickyGET('delete_task');
		$this->app->stickyGET('delete_rad_post_auth');

		$page->add('View_Console')->set(function($c){
			$c->out('--------*** (-_-) Reset Started  (-_-) ***--------');
			
			if($_GET['delete_all_invoice']){
				$sale_invoice = $this->add('xepan\commerce\Model_SalesInvoice');
				$c->out('--------*** Deleting Sales Invoice : total :'.$sale_invoice->count()->getOne().' ***--------');
				$i=1;
				foreach ($sale_invoice as $m){
					$m->delete();
					if($i%100 == 0)
						$c->out($i." record deleted");
					$i++;
				}
				$c->out(' All Sales Invoice Deleted Successfully');
			}

			if($_GET['delete_all_order']){
				$sale_order = $this->add('xepan\commerce\Model_SalesOrder');
				$c->out('--------*** Deleting Sales Order : total :'.$sale_order->count()->getOne().' ***--------');
				$i = 1;				
				foreach ($sale_order as $m){
					$m->delete();
					if($i%100 == 0)
						$c->out($i." record deleted");
					$i++;
				}
				$c->out(' All Sales Order Deleted Successfully');
			}

			if($_GET['delete_all_quotation']){
				$quo = $this->add('xepan\commerce\Model_Quotation');
				$c->out('--------*** Deleting Quotation : total :'.$quo->count()->getOne().' ***--------');
				$i = 1;
				foreach ($quo as $m){
					$m->delete();
					$c->out($i." record deleted");
					$i++;
				}
				$c->out(' All Quotation Deleted Successfully');
			}

			if($_GET['delete_all_stock']){
				$model = $this->add('xepan\commerce\Model_Store_TransactionAbstract');
				$c->out('--------*** Deleting Store Transaction : total :'.$model->count()->getOne().' ***--------');
				$i = 1;
				foreach ($model as $m){
					$m->delete();
					if($i%10 == 0)
						$c->out($i." record deleted");
					$i++;
				}
				$c->out(' All Quotation Deleted Successfully');
			}

			if($_GET['delete_all_support_ticket']){
				$model = $this->add('xepan\crm\Model_SupportTicket');
				$c->out('--------*** Deleting Support Ticket : total :'.$model->count()->getOne().' ***--------');
				$i = 1;
				foreach ($model as $m){
					$m->delete();
					if($i%10 == 0)
						$c->out($i." record deleted");
					$i++;
				}

				$model = $this->add('xepan\crm\Model_Ticket_Comments');
				$c->out('--------*** Deleting Support Ticket Comments : total :'.$model->count()->getOne().' ***--------');
				$i = 1;
				foreach ($model as $m){
					$m->delete();
					if($i%10 == 0)
						$c->out($i." record deleted");
					$i++;
				}
				$c->out(' All Support Ticket Comments Deleted Successfully');
			}

			if($_GET['delete_all_account_data']){
				$model = $this->add('xepan\accounts\Model_Transaction');
				$c->out('--------*** Deleting All Accounts Data : total :'.$model->count()->getOne().' ***--------');
				$i = 1;
				foreach ($model as $m){
					$m->delete();

					$c->out($i." record deleted");
					$i++;
				}
				$c->out('All Accounts Data Deleted Successfully');
			}

			// deleting activity
			$model = $this->add('xepan\base\Model_Activity');
			$c->out('--------*** Deleting All Activity : total :'.$model->count()->getOne().' ***--------');
			$i = 1;
			foreach ($model as $m) {
				$m->delete();
				if($i%100 == 0)
					$c->out($i." record deleted");
				$i++;
			}
			$c->out('--------*** All Activity Deleted Successfully');

			$model = $this->add('xepan\communication\Model_Communication');
			$c->out('--------*** Deleting All Communication : total :'.$model->count()->getOne().' ***--------');
			$i = 1;
			foreach ($model as $m) {
				$m->delete();
				if($i%100 == 0)
					$c->out($i." record deleted");
				$i++;
			}
			$c->out('--------*** All Communication Deleted Successfully ***--------');
			
			// delete isp user

			// delete task
			if($_GET['delete_task']){
				$model = $this->add('xepan\projects\Model_Task',['force_delete'=>1]);
				$c->out('--------*** Deleting All Task : total :'.$model->count()->getOne().' ***--------');
				$i = 1;
				foreach($model as $m){
					$m->delete();

					if($i%10==0)
						$c->out($i." record deleted");
					$i++;
				}
				$c->out('All Task Deleted Successfully');
			}

			if($_GET['delete_rad_post_auth']){
				$model = $this->add('xavoc\ispmanager\Model_RadPostAuth');
				$c->out('--------*** Deleting All Auth Data : total :'.$model->count()->getOne().' ***--------');
				$model->deleteAll();

				$this->app->db->dsql()->expr('ALTER TABLE radpostauth AUTO_INCREMENT = 1')->execute();

				$c->out('All Auth Data Successfully');
			}

			// contact created at
			if($_GET['contact_created_at']){
				$c->out('Updating Created Date of All Contact');
				$this->app->db->dsql()->expr("UPDATE contact SET created_at = '".$_GET['contact_created_at']." 00:00:00 '; ")->execute();
				$c->out('Updated all Contact created date');
			}

			$c->out('All Data Deleted Successfully ...| (-_-) ----- ');
		});

	}
}