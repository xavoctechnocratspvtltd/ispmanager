<?php

namespace xavoc\ispmanager;

class Tool_Notification extends \xepan\cms\View_Tool{

	public $options = [];

	function init(){
		parent::init();

		if($this->owner instanceof \AbstractController) return;
		
		$this->user = $user = $this->add('xavoc\ispmanager\Model_User');
		if(!$user->loadLoggedIn()){
			$this->add('View_Error')->set('login first to view notifications.');
			return;
		}

		$model = $this->add('xavoc\ispmanager\Model_Notification');
		$model->addCondition([['to_id',$user->id],['to_id',null]]);
		$model->setOrder('id','desc');
		$grid = $this->add('xepan\base\Grid');
		$grid->template->tryDel('Pannel');
		$grid->setModel($model,['created_at','title','description']);

		$grid->addHook('formatRow',function($g){
			$g->current_row_html['title'] = '<div class="row"> <div class="col-md-9 col-sm-12 col-lg-9"><strong>'.$g->model['title'].'</strong></div> <div class="col-md-3 col-sm-12 col-lg-3 text-right text-dimmed"><i class="fa fa-clock-o"></i>&nbsp;'.$g->model['created_at'].'</div></div>'.$g->model['description'];
		});
		$grid->removeColumn('description');
		$grid->removeColumn('created_at');
		$grid->addPaginator(10);
	}
}