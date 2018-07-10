<?php

namespace xavoc\ispmanager;


class page_upcominginvoicereminder extends \xepan\base\Page {
	
	public $title ="Up-Coming Invoice Reminder";

	function init(){
		parent::init();
		
		$this->config_model = $config_model = $this->add('xavoc\ispmanager\Model_Config_UpcomingInvoicesReminder');
		$config_model->tryLoadAny();
		if(!$config_model->loaded() OR !$config_model['send_reminder']){
			return;
		}
		
		// $last_invoice_date_between_days = 5;
		$this->on_date = $this->app->stickyGET('on_date')?:$this->app->today;
		$this->user_id = $this->app->stickyGET('user_id');
		$this->include_expired = $this->app->stickyGET('include_expired');
		
		$model = $this->getModel();

		$grid = $this->add('xepan\hr\Grid');
		$grid->fixed_header = false;
		$grid->setModel($model,$this->display_fields);
		$grid->addPaginator(25);
		$grid->add('View',null,'grid_heading_left')->set('On date: '.$this->on_date.' Total Record: '.$model->count()->getOne());

		// // manage list of notification data
		if(($this->config_model['sms_content'] && $this->config_model['sms_send_from']) OR ($this->config_model['email_subject'] && $this->config_model['email_body'] && $this->config_model['email_send_from'])){
			foreach ($model as $m) {
				if($this->config_model['sms_content'] && $this->config_model['sms_send_from'])
					$this->shootSMSReminder($m);

				if($this->config_model['email_subject'] && $this->config_model['email_body'] && $this->config_model['email_send_from']){
					$this->shootEmailReminder($m);
				}
			}
		}


	}

	function shootEmailReminder($m){

		$data_array = $m->data;

		$email_settings = $this->add('xepan\communication\Model_Communication_EmailSetting')
				->load($this->config_model['email_send_from']);

		$mail = $this->add('xepan\communication\Model_Communication_Email_Sent');

		$email_subject = $this->config_model['email_subject'];
		$email_body = $this->config_model['email_body'];
			
		$temp = $this->add('GiTemplate');
		$temp->loadTemplateFromString($email_body);

		$subject_temp = $this->add('GiTemplate');
		$subject_temp->loadTemplateFromString($email_subject);
		$subject_v = $this->app->add('View',null,null,$subject_temp);
		$subject_v->template->trySet($data_array);

		$temp = $this->add('GiTemplate');
		$temp->loadTemplateFromString($email_body);
		$body_v=$this->app->add('View',null,null,$temp);
		$body_v->template->trySet($data_array);					

		$mail->setfrom($email_settings['from_email'],$email_settings['from_name']);

		$email_array = $m->ref('user_id')->getEmails();
		$mail['related_contact_id'] = $m['user_id'];
		
		$email_array = array_unique($email_array);

		$total_email_id = 0;
		foreach ($email_array as $email_id) {
			if(!trim($email_id)) continue;
			$mail->addTo($email_id);
			$total_email_id += 1;
		}

		if(!$total_email_id) return;

		$mail['status'] = "Outbox";
		$mail->save();

		// attachment removed
		// if($m['send_document_as_attachment'] && $this->hasMethod('generatePDF')){
		// 	$file =	$this->add('xepan/filestore/Model_File',array('policy_add_new_type'=>true,'import_mode'=>'string','import_source'=>$this->generatePDF('return')));
		// 	$file['filestore_volume_id'] = $file->getAvailableVolumeID();
		// 	$file['original_filename'] =  strtolower($this['type']).'_'.$this['document_no_number'].'_'.$this->id.'.pdf';
		// 	$file->save();
		// 	$mail->addAttachment($file->id);
		// }

		$mail->setSubject($subject_v->getHtml());
		$mail->setBody($body_v->getHtml());
		$mail->send($email_settings);
	}

	function shootSMSReminder($m){

		$sms_setting = $this->add('xepan\communication\Model_Communication_SMSSetting')
			->load($this->config_model['sms_send_from']);

		$temp = $this->add('GiTemplate');
		$temp->loadTemplateFromString(trim($this->config_model['sms_content']));
		$msg = $this->app->add('View',null,null,$temp);
		$msg->template->trySet($m->data);

		$sms_commu = $this->add('xepan\communication\Model_Communication_SMS');
		$sms_commu->setBody($msg->getHtml());

		$phone_array = $m->add('xavoc\ispmanager\Model_User')
						->load($m['user_id'])
						->getPhones();
		$sms_commu['related_contact_id'] = $m['user_id'];

		$phone_array = array_unique($phone_array);
		
		$total_phone_no = 0;
		foreach ($phone_array as $number) {
			if(!$number) continue;

			$sms_commu->addTo($number);

			$total_phone_no += 1;
		}

		if(!$total_phone_no) return;
		$sms_commu->send($sms_setting);
	}

	function getModel(){

		$run_group =  $_GET['run_group']?:"yes";
		$last_invoice_status = explode(",",$this->config_model['send_on_invoice_status']?:'Due');
		$before_reminder_days = explode(",",$this->config_model['days_before_reminder']);
		$after_reminder_days = explode(",",$this->config_model['days_after_reminder']);

		$on_date = $this->on_date;
		$user_id = $this->user_id;
		$include_expired = $this->include_expired;

		$model = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup');
		$model->addExpression('radius_username')->set($model->refSQL("user_id")->fieldQuery('radius_username'));
		$model->addExpression('organization')->set($model->refSQL("user_id")->fieldQuery('organization'));
		$model->addExpression('sale_price')->set($model->refSQL('plan_id')->fieldQuery('sale_price'));
		$model->addExpression('plan_code')->set($model->refSQL('plan_id')->fieldQuery('sku'));

		$model->addExpression('last_invoice_date')->set(function($m,$q){
			$act = $m->add('xavoc\ispmanager\Model_Invoice')
					->addCondition('contact_id',$m->getElement('user_id'))
					->setOrder('id','desc')
					->setLimit(1);
			return $q->expr('IFNULL([0],0)',[$act->fieldQuery('created_at')]);
		})->type('date');

		$model->addExpression('last_invoice_no')->set(function($m,$q){
			$act = $m->add('xavoc\ispmanager\Model_Invoice')
					->addCondition('contact_id',$m->getElement('user_id'))
					->setOrder('id','desc')
					->setLimit(1);
			return $q->expr('CONCAT([0],[1])',[$act->fieldQuery('serial'),$act->fieldQuery('document_no')]);
		});

		$model->addExpression('last_invoice_status')->set(function($m,$q){
			$act = $m->add('xavoc\ispmanager\Model_Invoice')
					->addCondition('contact_id',$m->getElement('user_id'))
					->setOrder('id','desc')
					->setLimit(1);
			return $q->expr('[0]',[$act->fieldQuery('status')]);
		});

		$model->addExpression('user_status')->set($model->refSQL('user_id')->fieldQuery('status'));

		$display_fields = ['user','radius_username','plan_code','organization','customer','plan','last_invoice_date','is_between_range','last_invoice_status','end_date'];
		$model->getElement('end_date')->type('date');
		
		$date_condition = [['end_date',$on_date]];
		foreach ($before_reminder_days as $day) {
			$name = "before_day_".$day;
			// $before_date_field[] = $name;
			$display_fields[] = $name;
			
			$model->addExpression($name)->set(function($m,$q)use($day){
				return $q->expr('DATE_SUB([0], INTERVAL [1] DAY)',[$m->getElement('end_date'),$day]);
			})->type('date');

			$date_condition[] = [$name,$on_date];
		}

		foreach ($after_reminder_days as $day) {
			$name = "after_day_".$day;
			// $after_date_field[] = $name;
			$display_fields[] = $name;
			$model->addExpression($name)->set(function($m,$q)use($day){
				return $q->expr('ADDDATE([0],INTERVAL [1] DAY)',[$m->getElement('end_date'),$day]);
			})->type('date');

			$date_condition[] = [$name,$on_date];
		}

		$model->addCondition('user_status','Active');
		// $model->addCondition($date_condition);

		// note: if we apply this condition then upcomin invoices that invoice ie not made not show, it will only send invoices that  crated and due;
		// if($last_invoice_status && $last_invoice_date_between_days){
		// 	$inv_on_date_before = date('Y-m-d', strtotime($on_date. ' - '.$last_invoice_date_between_days.' days'));
		// 	$inv_on_date_after = date('Y-m-d', strtotime($on_date. ' + '.$last_invoice_date_between_days.' days'));
		// 	$model->addCondition('last_invoice_status',$last_invoice_status);
		// 	$model->addCondition('last_invoice_date','>=',$inv_on_date_before);
		// 	$model->addCondition('last_invoice_date','<',$inv_on_date_after);
		// }

		$model->setOrder('end_date','asc');
		// $model->addCondition('last_invoice_status','in',$last_invoice_status);
		// if($user_id){
		// 	$model->addCondition('user_id',$user_id);
		// }

		if($run_group == "yes"){
			$model->_dsql()->where('id in ( select max(id) from isp_user_plan_and_topup group by user_id)');
		}
		
		$this->display_fields = $display_fields;
		return $model;
	}
}