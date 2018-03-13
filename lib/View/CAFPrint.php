<?php

namespace xavoc\ispmanager;

class View_CAFPrint extends \View {

	function init(){
		parent::init();

		$contact_id = $this->app->stickyGET('contact_id');

		$config = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'caf_layout'=>'xepan\base\RichText'
					],
					'config_key'=>'ISPMANAGER_MISC',
					'application'=>'ispmanager'
			]);
		$config->tryLoadAny();

		$this->template->loadTemplateFromString($config['caf_layout']);


		$model = $this->add('xavoc\ispmanager\Model_User');
		$model->tryLoad($contact_id);

		$contact = $this->add('xepan\base\Model_Contact');
		$contact->tryLoad($contact_id);

		$data_array = $model->getRows()[0];
		$data_array = array_merge($contact->getRows()[0],$data_array);

		$attachment = $this->add('xavoc\ispmanager\Model_Attachment')->addCondition('contact_id',$contact_id);
		$attach_array = array_column($attachment->getRows(),'file','title');

		$data_array = array_merge($attach_array,$data_array);
		$this->template->set($data_array);
	}
}