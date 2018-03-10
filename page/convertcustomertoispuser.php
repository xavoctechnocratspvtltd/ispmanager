<?php

namespace xavoc\ispmanager;

class page_convertcustomertoispuser extends \xepan\base\Page {
	
	public $title = "Convert Customer to ISP User";
	function init(){
		parent::init();

		$new_ispuser_id = $this->app->stickyGET('ispuser_id');
		// lead to customer
		$f = $this->add('Form');
		$f->add('xepan\base\Controller_FLC')
			->makePanelsCoppalsible(true)
			->addContentSpot()
			->layout([
				'lead'=>'Convert Customer To ISP User~c1-12',
				'FormButtons~&nbsp;'=>'c1~12'
			]);

		$lead_model = $this->add('xepan\base\Model_Contact');
		$lead_model->addCondition([['type','Contact'],['type',null],['type','Lead'],['type','Customer']]);

		$contact = $f->addField('xepan\base\Basic','lead','customer')->validate('required');
		$contact->setModel($lead_model);

		// if($new_ispuser_id){
		// 	$f->add('View')->set('Please Update First ISP USER Detail: '.$new_ispuser_id);
		// }

		$f->addSubmit('Convert to Customer')->addClass('btn btn-primary');
		if($f->isSubmitted()){

			$lead_id = $f['lead'];

			$ispuser_m = $this->add('xavoc\ispmanager\Model_User');
			$ispuser_m->addCondition('id',$lead_id);
			$ispuser_m->tryLoadAny();
				
			try{
				$this->api->db->beginTransaction();
				

				$c_m = $this->add('xepan\base\Model_Contact');
				$c_m->addCondition('id',$lead_id);
				$c_m->tryLoadAny();

				if($c_m->loaded() && $c_m['type'] != "Customer"){
					// insert into isp_table table entry where conatct_id = $form['lead']
					$this->app->db->dsql()->table('customer')
											->set('contact_id',$lead_id)
											->set('billing_country_id',$c_m['country_id'])
											->set('billing_state_id',$c_m['state_id'])
											->set('billing_name',$c_m['first_name']." ".$c_m['last_name'])
											->set('billing_address',$c_m['address'])
											->set('billing_city',$c_m['city'])
											->set('billing_pincode',$c_m['pin_code'])
											->set('shipping_country_id',$c_m['country_id'])
											->set('shipping_state_id',$c_m['state_id'])
											->set('shipping_name',$c_m['first_name']." ".$c_m['last_name'])
											->set('shipping_address',$c_m['address'])
											->set('shipping_city',$c_m['city'])
											->set('shipping_pincode',$c_m['pin_code'])
											->insert();

					$this->app->db->dsql()->table('contact')
											->set('remark',$c_m['narration'])
											->set('type','Customer')
											->where('id',$lead_id)
											->update();
				}

				if(!$ispuser_m->loaded()){
					$this->app->db->dsql()->table('isp_user')
							->set('customer_id',$lead_id)
							->set('radius_username',$c_m['first_name'])
							->insert();
				}else{
					throw new \Exception("Selected customer already a ISP User");
				}

				$this->app->db->commit();
			}catch(\Exception $e){
				$this->api->db->rollback();
				throw $e;
			}

			$ispuser_m->tryLoadAny();
			$f->js(null,$f->js()->reload(['ispuser_id'=>$ispuser_m->id]))->univ()->successMessage('Customer Converted to ISP User')->execute();
		}
	}
}