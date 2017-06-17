<?php

namespace xavoc\ispmanager;


/**
* 
*/
class Tool_PurchasePlan extends \xepan\cms\View_Tool{
	public $options = [
						'show_purchase_btn'=>true,
						'purchase_btn_name'=>'Recharge Now',
						'checkout_page_url'=>'checkout',
						'show_description'=>true,
						'paginator_set_rows_per_page'=>20,
						'layout_template'=>'purchaseplan',
						'show_only'=>'plan', // topup or both
						'checkout_page'=>'checkout'
				];

	function init(){
		parent::init();
				
		$layout_template = 'purchaseplan';
		if($this->options['layout_template']) $layout_template = $this->options['layout_template'];

		$plan_model = $this->add('xavoc\ispmanager\Model_Plan');
		$plan_model->addCondition('status','Published');
		// $plan_model->addCondition('available_in_user_control_panel',true);

		$this->complete_lister = $cl = $this->add('CompleteLister',null,null,['xavoc/tool/'.$layout_template]);
		$cl->setModel($plan_model);
		$paginator = $cl->add('Paginator',['ipp'=>$this->options['paginator_set_rows_per_page']]);
		$paginator->setRowsPerPage($this->options['paginator_set_rows_per_page']);
		
		// deleting not found templates
		if($plan_model->count()->getOne()){
			$cl->template->del('not_found');
		}else{
			$cl->template->set('not_found_message','No Record Found');
		}

		$cl->add('xepan\cms\Controller_Tool_Optionhelper',['options'=>$this->options,'model'=>$plan_model]);

		$this->on('click','.xepan-isp-plan-purchase-button',function($js,$data){

			$result = $this->placeOrder($data['xplanid']);

			if($result['status'] == "success"){
				$checkout_page = $this->options['checkout_page']?:"checkout";
				$url = $this->app->url($checkout_page,['order_id'=>$result['order_id'],'step'=>'payment','pay_now'=>true]);
				$js_event = [
					$js->univ()->redirect($url)
				]; 
				return $js->univ(null,$js_event)->successMessage($result['message']);
			}else{
				return $js->univ()->errorMessage($result['message']);
			}
		});		
	}

	function placeOrder($plan_id){		
		$plan_model = $this->add('xavoc\ispmanager\Model_Plan');
		return $plan_model->placeOrder($plan_id);
	}

	function addToolCondition_row_show_purchase_btn($value,$l){
		$btn_label = "Recharge Now";
		if($this->options['purchase_btn_name']) $btn_label = $this->options['purchase_btn_name'];
		
		if($value != true){
			$l->current_row_html['purchase_btn_wrapper'] = "";
			return;
		}

		$btn = $l->add('Button',null,'purchase_btn');
		$btn->addClass('btn btn-primary btn-block');
		$btn->setAttr('data-xplanid',$l->model->id);
		$btn->set($btn_label);
		$l->current_row_html['purchase_btn'] = $btn->getHtml();
	}
}