<?php


namespace xavoc\ispmanager;


class Controller_HumanByte extends \AbstractController {
	
	function handleFields($fields){
		if(!is_array($fields)) throw $this->exception('Fields must be Array');

		$this->owner->addHook('beforeSave',function($m)use($fields){
			foreach ($fields as $field) {
				if($m[$field]!='')
					$m[$field] = $this->app->human2byte($m[$field]);
				else
					$m[$field]=null;
			}
		});

		$this->owner->addHook('afterLoad',function($m)use($fields){
			foreach ($fields as $field) {
				if($m[$field]!==null)
					$m[$field] = $this->app->byte2human($m[$field]);
			}
		});
	}
}