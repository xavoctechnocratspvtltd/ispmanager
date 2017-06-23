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

class page_tests_003DayNight extends page_Tester {
	
	public $title='Day Night Plan';
	
	public $proper_responses=[''];

    public $user;
    public $on_date;

    function init(){
        $this->user = $this->add('xavoc\ispmanager\Model_User')->loadBy('name','test user');
        // $this->user->setPlan('PL-50-M','2017-05-01',true);
        parent::init();
    }

    function test_setplan(){
        $r = $this->process([
                '2017-05-01 00:00:00'=>'plan-Day Night plan',
                '2017-05-01 08:01:00'=>'getdata'
            ]);
        return $r;
    }

    function prepare_setplan(){
        $this->proper_responses['test_setplan']=[
            [   
                'user'=>'Test User',
                'plan'=>'Day Night plan',
                'remark'=>'Night Plan',
                'data_limit'=>'10.00GB',
                'time_limit'=>0,
                'download_limit'=>'2.00MB',
                'upload_limit'=>'2.00MB',
                'fup_download_limit'=>null,
                'fup_upload_limit'=>null,
                'accounting_download_ratio'=>'100',
                'accounting_upload_ratio'=>'100',
                'is_data_carry_forward'=>0,
                'start_time'=>'20:00:00',
                'end_time'=>'08:00:00',
                'start_date'=>'2017-05-01 00:00:00',
                'end_date'=>'2017-06-01 00:00:00',
                'expire_date'=>'2017-06-06 00:00:00',
                'is_expired'=>0,
                'is_recurring'=>1,
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
                'd31'=>1,
                'treat_fup_as_dl_for_last_limit_row'=>0
            ],
            [   
                'user'=>'Test User',
                'plan'=>'Day Night plan',
                'remark'=>'Day Plan',
                'data_limit'=>'10.00GB',
                'time_limit'=>0,
                'download_limit'=>'2.00MB',
                'upload_limit'=>'2.00MB',
                'fup_download_limit'=>null,
                'fup_upload_limit'=>null,
                'accounting_download_ratio'=>'100',
                'accounting_upload_ratio'=>'100',
                'is_data_carry_forward'=>0,
                'start_time'=>'08:00:00',
                'end_time'=>'20:00:00',
                'start_date'=>'2017-05-01 00:00:00',
                'end_date'=>'2017-06-01 00:00:00',
                'expire_date'=>'2017-06-06 00:00:00',
                'is_expired'=>0,
                'is_recurring'=>1,
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
                'd31'=>1,
                'treat_fup_as_dl_for_last_limit_row'=>0
            ]
        ];
    }


    function test_nightEffectiveRow(){
        $r = $this->process([
                '2017-05-01 00:00:00'=>'plan-Day Night plan',
                '2017-05-01 00:01:00'=>'authentication'
            ]);
        return $this->result($r);
    }

    function prepare_nightEffectiveRow(){
        $this->proper_responses['test_nightEffectiveRow']=[
            'data_limit_row'=>'Night Plan',
            'bw_limit_row'=>'Night Plan',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'0.00B',
            'time_limit'=>0,
            'time_consumed'=>0,
            'access'=>1,
            'coa' => 1
        ];
    }

    function test_dayEffectiveRow(){
        $r = $this->process([
                '2017-05-01 00:00:00'=>'plan-Day Night plan',
                '2017-05-01 08:01:00'=>'authentication'
            ]);
        return $this->result($r);
    }

    function prepare_dayEffectiveRow(){
        $this->proper_responses['test_dayEffectiveRow']=[
            'data_limit_row'=>'Day Plan',
            'bw_limit_row'=>'Day Plan',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'0.00B',
            'time_limit'=>0,
            'time_consumed'=>0,
            'access'=>1,
            'coa' => 1
        ];
    }

    function test_MaintainData(){
        $r = $this->process([
                '2017-05-01 00:00:00'=>'plan-Day Night plan',
                '2017-05-01 07:50:00'=>'authentication',
                '2017-05-01 07:55:00'=>'1mb',
                '2017-05-01 08:00:00'=>'1mb',
                '2017-05-02 08:10:00'=>'1mb',
                '2017-05-02 08:15:00'=>'10gb',
                '2017-05-03 08:10:00'=>'login',
            ]);
        return $this->result($r);
    }

    function prepare_MaintainData(){
        $this->proper_responses['test_MaintainData']=[
            'data_limit_row'=>'Day Plan',
            'bw_limit_row'=>'Day Plan',
            'dl'=>null,
            'ul'=>null,
            'data_consumed'=>'10.00GB',
            'time_limit'=>0,
            'time_consumed'=>0,
            'access'=>0,
            'coa' => 1
        ];
    }

    function test_10_05_dataConsume(){
        $r = $this->process([
                '2017-05-01 00:00:00'=>'plan-Day Night plan',
                '2017-05-10 22:30:00'=>'authentication',
                '2017-05-10 22:35:00'=>'10gb',
            ]);
        return $this->result($r);
    }

    function prepare_10_05_dataConsume(){
        $this->proper_responses['test_10_05_dataConsume']=[
            'data_limit_row'=>'Night Plan',
            'bw_limit_row'=>'Night Plan',
            'dl'=>'',
            'ul'=>'',
            'data_consumed'=>'10.00GB',
            'time_limit'=>0,
            'time_consumed'=>0,
            'access'=>0,
            'coa' => 1
        ];
    }

    function test_15_05_dataConsumed(){
        $r = $this->process([
                '2017-05-01 00:00:00'=>'plan-Day Night plan',
                '2017-05-10 22:30:00'=>'authentication',
                '2017-05-10 22:35:00'=>'1gb',
                '2017-05-12 22:35:00'=>'5gb',
                '2017-05-13 22:35:00'=>'2gb',
                '2017-05-13 22:40:00'=>'2gb',
                '2017-05-13 22:45:00'=>'50mb',
            ]);
        return $this->result($r);
    }

    function prepare_15_05_dataConsumed(){
        $this->proper_responses['test_15_05_dataConsumed']=[
            'data_limit_row'=>'Night Plan',
            'bw_limit_row'=>'Night Plan',
            'dl'=>null,
            'ul'=>null,
            'data_consumed'=>'10.05GB',
            'time_limit'=>0,
            'time_consumed'=>0,
            'access'=>0,
            'coa' => 0
        ];
    }

    function test_1_06(){
        $this->setDateTime('2017-06-01 00:01:00'); // next month after disconnect
        $r = $this->process([
            '2017-06-01 00:00:00'=>'plan-Day Night plan',
            '2017-06-10 01:10:00'=>'authentication'
        ]);

        return $this->result($r);
    }

    function prepare_1_06(){
        $this->proper_responses['test_1_06']=[
            'data_limit_row'=>'Night Plan',
            'bw_limit_row'=>'Night Plan',
            'data_consumed'=>'0.00B',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'time_limit'=>0,
            'time_consumed'=>0,
            'access'=>1,
            'coa' => 0
        ];
    }

}
