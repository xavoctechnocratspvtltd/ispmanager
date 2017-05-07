<?php

/**
* description: ATK Page
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xavoc\ispmanager;

class page_tests_001PlanSetToUserTest extends page_Tester {
	
	public $title='Test';
	
	public $proper_responses=[''];

    public $user;
    public $on_date;

    function init(){
        $this->user = $this->add('xavoc\ispmanager\Model_User')->loadBy('name','test user');
        parent::init();
    }

    

    function test_setplan_multirow($fields){
        $this->user->setPlan('SUNDAY EXCLUDED 100GB-1m','2017-05-01',true);
        $model = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup')
            ->addCondition('user_id',$this->user->id);
        $data=[];
        foreach ($model as $m) {
            $data[] =$m->data;    
        }

        return $this->filterColumns($data,$fields);
    }

    function prepare_setplan_multirow(){
        $this->proper_responses['test_setplan_multirow']=[
            [   
                'user'=>'Test User',
                'plan'=>'SUNDAY EXCLUDED 100GB-1m',
                'data_limit'=>'100.00GB',
                'download_limit'=>'2.00MB',
                'upload_limit'=>'2.00MB',
                'fup_download_limit'=>'512.00KB',
                'fup_upload_limit'=>'512.00KB',
                'accounting_download_ratio'=>'100',
                'accounting_upload_ratio'=>'100',
                'start_date'=>'2017-05-01 00:00:00',
                'end_date'=>'2017-06-01 00:00:00',
                'expire_date'=>'2017-06-06 00:00:00',
                'is_expired'=>0,
                'is_recurring'=>1,
                'is_data_carry_forward'=>0,
                'start_time'=>null,
                'end_time'=>null,
                'reset_date'=>'2017-06-01 00:00:00',
                'sun'=>1,
                'mon'=>1,
                'tue'=>1,
                'wed'=>1,
                'thu'=>1,
                'fri'=>1,
                'sat'=>1,
                'd01'=>1,
                'd02'=>1,
                'd03'=>1,
                'd04'=>1,
                'd05'=>1,
                'd06'=>1,
                'd07'=>1,
                'd08'=>1,
                'd09'=>1,
                'd10'=>1,
                'd11'=>1,
                'd12'=>1,
                'd13'=>1,
                'd14'=>1,
                'd15'=>1,
                'd16'=>1,
                'd17'=>1,
                'd18'=>1,
                'd19'=>1,
                'd20'=>1,
                'd21'=>1,
                'd22'=>1,
                'd23'=>1,
                'd24'=>1,
                'd25'=>1,
                'd26'=>1,
                'd27'=>1,
                'd28'=>1,
                'd29'=>1,
                'd30'=>1,
                'd31'=>1
            ],
            [   
                'user'=>'Test User',
                'plan'=>'SUNDAY EXCLUDED 100GB-1m',
                'data_limit'=>null,
                'download_limit'=>null,
                'upload_limit'=>null,
                'fup_download_limit'=>null,
                'fup_upload_limit'=>null,
                'accounting_download_ratio'=>'0',
                'accounting_upload_ratio'=>'0',
                'start_date'=>'2017-05-01 00:00:00',
                'end_date'=>'2017-06-01 00:00:00',
                'expire_date'=>'2017-06-06 00:00:00',
                'is_expired'=>0,
                'is_recurring'=>1,
                'is_data_carry_forward'=>0,
                'start_time'=>null,
                'end_time'=>null,
                'reset_date'=>null,
                'sun'=>1,
                'mon'=>0,
                'tue'=>0,
                'wed'=>0,
                'thu'=>0,
                'fri'=>0,
                'sat'=>0,
                'd01'=>1,
                'd02'=>1,
                'd03'=>1,
                'd04'=>1,
                'd05'=>1,
                'd06'=>1,
                'd07'=>1,
                'd08'=>1,
                'd09'=>1,
                'd10'=>1,
                'd11'=>1,
                'd12'=>1,
                'd13'=>1,
                'd14'=>1,
                'd15'=>1,
                'd16'=>1,
                'd17'=>1,
                'd18'=>1,
                'd19'=>1,
                'd20'=>1,
                'd21'=>1,
                'd22'=>1,
                'd23'=>1,
                'd24'=>1,
                'd25'=>1,
                'd26'=>1,
                'd27'=>1,
                'd28'=>1,
                'd29'=>1,
                'd30'=>1,
                'd31'=>1
            ]
        ];
        return [array_keys($this->proper_responses['test_setplan_multirow'][0])];
    }

}