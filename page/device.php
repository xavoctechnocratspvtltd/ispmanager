<?php

namespace xavoc\ispmanager;


class page_device extends \xepan\base\Page {
	
	public $title ="Device Management";

	function init(){
		parent::init();

		$vp = $this->add('VirtualPage');
		$this->manageDBConfigVP($vp);

		$vp1 = $this->add('VirtualPage');
		$this->manageGenerateConfigVP($vp1);
		

		$crud = $this->add('xepan\hr\CRUD');
		$crud->setModel('xavoc\ispmanager\Device');

		$crud->grid->add('Button',null,'grid_buttons')->set('Host Config')->addClass('btn btn-primary')->js('click')->univ()->frameURL($vp->getURL());
		$crud->grid->add('Button',null,'grid_buttons')->set('Generate Config')->addClass('btn btn-primary')->js('click')->univ()->frameURL($vp1->getURL());

	}

	function manageDBConfigVP($vp){
		$vp->set(function ($page){
			$db_config = $this->add('xepan\base\Model_ConfigJsonModel',
				[
					'fields'=>[
								'mysql_host'=>'Line',
								'mysql_port'=>'Line',
								'mysql_user'=>'Line',
								'mysql_password'=>'Line',
								'xepan_host'=>'Line',
								],
						'config_key'=>'DB_CONFIG_FOR_MONIT',
						'application'=>'ispmanager'
				]);
			$db_config->add('xepan\hr\Controller_ACL');
			$db_config->tryLoadAny();
			$f = $page->add('Form');
			$f->setModel($db_config);
			$f->addSubmit('Save');

			if($f->isSubmitted()){
				$f->save();
				$f->js()->univ()->successMessage('saved')->closeDialog()->execute();
			}

		});
	}

/**

check host localhost with address 127.0.0.1
      if failed ping then alert        
      if failed port 3306 protocol mysql then alert

*/

	function manageGenerateConfigVP($vp){
		$db_config = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							// 'mysql_host'=>'Line',
							// 'mysql_port'=>'Line',
							// 'mysql_user'=>'Line',
							// 'mysql_password'=>'Line',
							'xepan_host'=>'Line',
							],
					'config_key'=>'DB_CONFIG_FOR_MONIT',
					'application'=>'ispmanager'
			]);
		$db_config->add('xepan\hr\Controller_ACL');
		$db_config->tryLoadAny();

		$vp->set(function($page)use($db_config){
			$devices = $this->add('xavoc\ispmanager\Model_Device');
			$config_file=[];

			foreach ($devices as $d) {
				$d['failed_action'] = str_replace('{xepan_host}', $db_config['xepan_host'], $d['failed_action']);
				$d['failed_action'] = str_replace('{device_id}', $d->id, $d['failed_action']);

				$d['override_failed_action'] = str_replace('{xepan_host}', $db_config['xepan_host'], $d['override_failed_action']);
				$d['override_failed_action'] = str_replace('{device_id}', $d->id, $d['override_failed_action']);


				if($d['override_check_line']){
					$config_file[] = $d['override_check_line'];
				}
				elseif($d['monitor']=='ping'){
					$config_file[] = "check host ". $this->app->normalizeName($d['name']) . " with address ". $d['ip'];					
				}elseif($d['monitor']=='host-port'){
					$config_file[] = "check host ". $this->app->normalizeName($d['name']) . " with address ". $d['ip'];
				}

				if($d['override_failed_action']){
					$config_file[] = $d['override_failed_action'];
				}
				else{
					$for_cycle = '';
					if($d['allowed_fail_cycle']){
						$for_cycle='for '. $d['allowed_fail_cycle'].' cycle';
					}
					$type="";
					if($d['type']){
						$type= " type ".$d['type'];
					}
					$protocol="";
					if($d['protocol']){
						$protocol = " protocol ". $d['protocol'];
					}

					if($d['monitor']=='ping'){
						$config_file[] = "\tif failed ping $for_cycle then " . $d['failed_action'];
					}elseif($d['monitor']=='host-port'){
						$config_file[] = "\tif failed port ".$d['port']." $type $protocol $for_cycle then " . $d['failed_action'];
					}
				}
			}

			$page->add('View')->setElement('textarea')->setAttr('rows',20)->set(implode("\n", $config_file));

		});
	}
}