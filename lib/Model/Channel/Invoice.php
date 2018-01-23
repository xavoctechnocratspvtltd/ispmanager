<?php

namespace xavoc\ispmanager;

class Model_Channel_Invoice extends \xavoc\ispmanager\Model_Invoice{ 
	
	public $status = ['Draft','Submitted','Redesign','Due','Paid','Canceled'];
	public $actions = [
			'Draft'=>['view','edit','delete','submit','manage_attachments'],
			'Submitted'=>['view','edit','delete','redesign','approve','manage_attachments','print_document'],
			'Redesign'=>['view','edit','delete','submit','manage_attachments'],
			'Due'=>['view','edit','delete','redesign','paid','send','cancel','manage_attachments','print_document'],
			'Paid'=>['view','edit','delete','send','cancel','manage_attachments','print_document'],
			'Canceled'=>['view','edit','delete','redraft','manage_attachments']
		];

	function init(){
		parent::init();
		
		$join = $this->join('isp_channel_association.invoice_id');
		$join->addField('channel_id');
		
		$this->add('misc/Field_Callback','net_amount_client_currency')->set(function($m){
			return $m['exchange_rate'] == '1'? "": ($m['net_amount'].' '. $m['currency']);
		});

		$this->addExpression('contact_type',$this->refSQL('contact_id')->fieldQuery('type'));

		$this->addExpression('contact_name',function($m,$q){
			return $m->refSQL('contact_id')->fieldQuery('name');
		});

		$this->addExpression('contact_organization_name',function($m,$q){
			return $m->refSQL('contact_id')->fieldQuery('organization');
		});

		$this->addExpression('organization_name',function($m,$q){
			return $q->expr('IF(ISNULL([organization_name]) OR trim([organization_name])="" ,[contact_name],[organization_name])',
						[
							'contact_name'=>$m->getElement('contact_name'),
							'organization_name'=>$m->getElement('contact_organization_name')
						]
					);
		});

		$this->addExpression('ord_no',function($m,$q){
			return $m->refSQL('related_qsp_master_id')->fieldQuery('document_no');
		});

		$this->addExpression('sales_order_id',function($m,$q){
			return $m->refSQL('related_qsp_master_id')->fieldQuery('id');
		});
		
	}
}