<?php

namespace xavoc\ispmanager;


class page_debug extends \xepan\base\Page {
	
	public $title ="Server Debug";

	function init(){
		parent::init();

		$this->add('H1')->set('Check Problem with Radius Server and NAS communciation');
		$vp = $this->add('VirtualPage');
		$vp->set([$this,'showRadiusLogTail']);

		$form = $this->add('Form');
		$form->addField('radius_log_path')->set('/var/log/freeradius/radius.log');
		$form->addField('show_last_lines')->set(100);

		$form->addSubmit('Show')->addClass('btn btn-primary');

		if($form->isSubmitted()){
			$form->js()->univ()->frameURL($this->app->url($vp->getURL(),['radius_log_path'=>$form['radius_log_path'],'show_last_lines'=>$form['show_last_lines']]))->execute();
		}


		$this->add('H1')->set('Check what NAS and RADIUS are comunication about a user');
		$vp = $this->add('VirtualPage');
		$vp->set([$this,'showRadDebug']);

		$form = $this->add('Form');
		$form->addField('radius_username')->validate('required');
		$form->addField('accounting_gap')->set(300)->setFieldHint('In seconds');

		$form->addSubmit('Catch Radius Packets')->addClass('btn btn-primary');

		if($form->isSubmitted()){
			$form->js()->univ()->frameURL($this->app->url($vp->getURL(),['radius_username'=>$form['radius_username'],'accounting_gap'=>$form['accounting_gap']]))->execute();
		}
	}

	function showRadiusLogTail($page){
		$cmd = 'tail -n '. $_GET['show_last_lines'].' '. $_GET['radius_log_path'] .' 2>&1';

		$v = $page->add('View');
		$output = shell_exec($cmd);

		$v->setHTML('<h3>'.$cmd.'</h3><pre>'.$output.'</pre>');
		ob_flush();
	}

	function showRadDebug($page){
		$cmd = 'raddebug -u '. $_GET['radius_username'].' -t '. $_GET['accounting_gap']  .' 2>&1';

		$v = $page->add('View');
		$output = shell_exec($cmd);

		$v->setHTML('<h3>'.$cmd.'</h3><pre>'.$output.'</pre>');
		ob_flush();
	}
}