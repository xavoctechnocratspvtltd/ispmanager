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

class page_tests_009NightUnlimited extends page_Tester {
	
	public $title='Night Unlimited Plan';
	
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
                '2017-05-01 00:00:00'=>'plan-Night Unlimited',
                '2017-05-01 08:01:00'=>'getdata'
            ]);
        return $r;
    }

    function prepare_setplan(){
        $this->proper_responses['test_setplan']=[
            [   
                'user'=>'Test User',
                'plan'=>'Night Unlimited',
                'remark'=>'Main Plan',
                'data_limit'=>'100.00GB',
                'time_limit'=>0,
                'download_limit'=>'2.00MB',
                'upload_limit'=>'2.00MB',
                'fup_download_limit'=>'512.00KB',
                'fup_upload_limit'=>'512.00KB',
                'accounting_download_ratio'=>'100',
                'accounting_upload_ratio'=>'100',
                'is_data_carry_forward'=>0,
                'start_time'=>null,
                'end_time'=>null,
                'start_date'=>'2017-05-01 00:00:00',
                'end_date'=>'2017-06-01 00:00:00',
                'expire_date'=>'2017-06-06 00:00:00',
                'is_expired'=>0,
                'is_recurring'=>1,
                'reset_date'=>'2017-06-01 00:00:00',
                // 'data_reset_value'=>1,
                // 'data_reset_mode'=>'months',
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
                'plan'=>'Night Unlimited',
                'remark'=>'Night Unlimited',
                'data_limit'=>null,
                'time_limit'=>0,
                'download_limit'=>"",
                'upload_limit'=>"",
                'fup_download_limit'=>null,
                'fup_upload_limit'=>null,
                'accounting_download_ratio'=>'0',
                'accounting_upload_ratio'=>'0',
                'is_data_carry_forward'=>0,
                'start_time'=>'22:00:00',
                'end_time'=>'04:00:00',
                'start_date'=>'2017-05-01 00:00:00',
                'end_date'=>'2017-06-01 00:00:00',
                'expire_date'=>'2017-06-06 00:00:00',
                'is_expired'=>0,
                'is_recurring'=>1,
                'reset_date'=>null,
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


    function test_mainPlanEffectiveRow(){
        $r = $this->process([
                '2017-05-01 05:00:00'=>'plan-Night Unlimited',
                '2017-05-01 05:01:00'=>'authentication'
            ]);
        return $this->result($r);
    }

    function prepare_mainPlanEffectiveRow(){
        $this->proper_responses['test_mainPlanEffectiveRow']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'0.00B',
            'time_limit'=>0,
            'time_consumed'=>0,
            'access'=>1,
            'coa' => 1
        ];
    }

    function test_nightEffectiveRow(){
        $r = $this->process([
                '2017-05-01 02:00:00'=>'plan-Night Unlimited',
                '2017-05-01 02:01:00'=>'authentication'
            ]);
        return $this->result($r);
    }

    function prepare_nightEffectiveRow(){
        $this->proper_responses['test_nightEffectiveRow']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Night Unlimited',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'0.00B',
            'time_limit'=>0,
            'time_consumed'=>0,
            'access'=>1,
            'coa' => 1
        ];
    }

    function test_05_01MaintainData(){
        $r = $this->process([
                '2017-05-01 05:00:00'=>'plan-Night Unlimited',
                '2017-05-01 05:01:00'=>'authentication',
                '2017-05-01 05:20:00'=>'2gb',
                '2017-05-01 10:30:00'=>'2gb',
                '2017-05-01 14:10:00'=>'1gb',
            ]);
        return $this->result($r);
    }

    function prepare_05_01MaintainData(){
        $this->proper_responses['test_05_01MaintainData']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'5.00GB',
            'time_limit'=>0,
            'time_consumed'=>0,
            'access'=>1,
            'coa'=>0
        ];
    }

    function test_dataConsume(){
        $r = $this->process([
                '2017-05-01 05:00:00'=>'plan-Night Unlimited',
                '2017-05-01 05:01:00'=>'authentication',
                '2017-05-01 05:20:00'=>'2gb',
                '2017-05-01 10:30:00'=>'2gb',
                '2017-05-01 14:10:00'=>'1gb',
                '2017-05-01 20:10:00'=>'4gb',
                '2017-05-01 23:10:00'=>'5gb',
            ]);
        return $this->result($r);
    }

    function prepare_dataConsume(){
        $this->proper_responses['test_dataConsume']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Night Unlimited',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'14.00GB',
            'time_limit'=>0,
            'time_consumed'=>0,
            'access'=>1,
            'coa'=> 1
        ];
    }

    function test_dataConsumeInNight(){
        $r = $this->process([
                '2017-05-01 23:00:00'=>'plan-Night Unlimited',
                '2017-05-01 23:10:00'=>'authentication',
                '2017-05-02 01:10:00'=>'10gb',
                '2017-05-02 01:59:59'=>'12gb',
                '2017-05-02 03:59:59'=>'20gb',
            ]);
        return $this->result($r);
    }

    function prepare_dataConsumeInNight(){
        $this->proper_responses['test_dataConsumeInNight']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Night Unlimited',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'0.00B',
            'time_limit'=>0,
            'time_consumed'=>0,
            'access'=>1,
            'coa'=>0
        ];
    }
    

    // function test_15_05_dataConsumed(){
    //     $r = $this->process([
    //             '2017-05-01 00:00:00'=>'plan-PL-50-M',
    //             '2017-05-10 22:30:00'=>'authentication',
    //             '2017-05-10 22:35:00'=>'10gb',
    //             '2017-05-12 22:35:00'=>'20gb',
    //             '2017-05-13 22:35:00'=>'19gb',
    //             '2017-05-13 22:40:00'=>'1gb',
    //             '2017-05-13 22:45:00'=>'1mb',
    //             '2017-05-13 22:46:00'=>'authentication',
    //         ]);
    //     return ['data_limit'=>$r['result']['data_limit'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    // }

    // function prepare_15_05_dataConsumed(){
    //     $this->proper_responses['test_15_05_dataConsumed']=[
    //         'data_limit'=>'50.00GB',
    //         'dl'=>null,
    //         'ul'=>null,
    //         'data_consumed'=>'50.00GB',
    //         'access'=>0
    //     ];
    // }

}
