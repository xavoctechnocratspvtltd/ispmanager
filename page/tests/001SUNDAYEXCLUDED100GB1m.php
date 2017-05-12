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

class page_tests_001SUNDAYEXCLUDED100GB1m extends page_Tester {
	
	public $title='001PlanSetToUserTest';
	
	public $proper_responses=[''];

    public $user;
    public $on_date;

    function init(){
        $this->user = $this->add('xavoc\ispmanager\Model_User')->loadBy('name','test user');
        parent::init();
    }

    

    function test_setplan_multirow(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-SUNDAY EXCLUDED 100GB-1m',
                '2017-01-10 00:00:00'=>'top-Top-7Days-10MB-HighSpeed',
                '2017-01-01 00:01:00'=>'getdata'
            ]);
        return $r;
    }

    function prepare_setplan_multirow(){
        $this->proper_responses['test_setplan_multirow']=[
            [   
                'user'=>'Test User',
                'plan'=>'SUNDAY EXCLUDED 100GB-1m',
                'remark'=>'All Day Plan',
                'data_limit'=>'100.00GB',
                'download_limit'=>'2.00MB',
                'upload_limit'=>'2.00MB',
                'fup_download_limit'=>'512.00KB',
                'fup_upload_limit'=>'512.00KB',
                'accounting_download_ratio'=>'100',
                'accounting_upload_ratio'=>'100',
                'start_date'=>'2017-01-01 00:00:00',
                'end_date'=>'2017-02-01 00:00:00',
                'expire_date'=>'2017-02-06 00:00:00',
                'is_expired'=>0,
                'is_recurring'=>1,
                'is_data_carry_forward'=>'none',
                'start_time'=>null,
                'end_time'=>null,
                'reset_date'=>'2017-02-01 00:00:00',
                'data_reset_value'=>'1',
                'data_reset_mode'=>'months',
                'is_topup'=>0,
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
                'remark'=>'Sunday Offer',
                'data_limit'=>null,
                'download_limit'=>null,
                'upload_limit'=>null,
                'fup_download_limit'=>null,
                'fup_upload_limit'=>null,
                'accounting_download_ratio'=>'0',
                'accounting_upload_ratio'=>'0',
                'start_date'=>'2017-01-01 00:00:00',
                'end_date'=>'2017-02-01 00:00:00',
                'expire_date'=>'2017-02-06 00:00:00',
                'is_expired'=>0,
                'is_recurring'=>1,
                'is_data_carry_forward'=>'none',
                'start_time'=>null,
                'end_time'=>null,
                'reset_date'=>null,
                'data_reset_value'=>null,
                'data_reset_mode'=>null,
                'is_topup'=>0,
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
            ],
            [   
                'user'=>'Test User',
                'plan'=>'Top-7Days-10MB-HighSpeed',
                'remark'=>'Main Topup',
                'data_limit'=>null,
                'download_limit'=>'10.00MB',
                'upload_limit'=>'10.00MB',
                'fup_download_limit'=>null,
                'fup_upload_limit'=>null,
                'accounting_download_ratio'=>'100',
                'accounting_upload_ratio'=>'100',
                'start_date'=>'2017-01-10 00:00:00',
                'end_date'=>'2017-01-17 00:00:00',
                'expire_date'=>'2017-01-17 00:00:00',
                'is_expired'=>0,
                'is_recurring'=>0,
                'is_data_carry_forward'=>'none',
                'start_time'=>null,
                'end_time'=>null,
                'reset_date'=>null,
                'data_reset_value'=>null,
                'data_reset_mode'=>null,
                'is_topup'=>1,
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
            ]
        ];
    }


    function test_allDayPlan(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-SUNDAY EXCLUDED 100GB-1m',
                '2017-01-01 00:01:00'=>'authentication'
            ]);
        return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    }

    function prepare_allDayPlan(){
        $this->proper_responses['test_allDayPlan']=[
            'data_limit_row'=>'All Day Plan',
            'bw_limit_row'=>'All Day Plan',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'0.00B',
            'access'=>1
        ];
    }

    function test_includeSunday(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-SUNDAY EXCLUDED 100GB-1m',
                '2017-01-01 00:01:00'=>'authentication',
                '2017-01-01 07:55:00'=>'1gb',
                '2017-01-07 23:55:00'=>'2mb',
                '2017-01-08 00:00:01'=>'1mb',
                '2017-01-08 08:00:00'=>'10gb',
            ]);
        return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    }

    function prepare_includeSunday(){
        $this->proper_responses['test_includeSunday']=[
            'data_limit_row'=>'All Day Plan',
            'bw_limit_row'=>'Sunday Offer',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'2.00MB',
            'access'=>1
        ];
    }

    function test_25_01_dataConsumed(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-SUNDAY EXCLUDED 100GB-1m',
                '2017-01-01 00:01:00'=>'authentication',
                '2017-01-01 00:00:00'=>'2gb',
                '2017-01-12 23:45:00'=>'10mb',
                '2017-01-12 23:55:00'=>'1mb',
                '2017-01-13 00:00:00'=>'10mb',
                '2017-01-15 00:00:00'=>'2gb',
                '2017-01-18 00:00:00'=>'2gb',
                '2017-01-25 00:00:00'=>'2gb',
                '2017-01-25 08:12:00'=>'authentication',
            ]);
        return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    }

    function prepare_25_01_dataConsumed(){
        $this->proper_responses['test_25_01_dataConsumed']=[
            'data_limit_row'=>'All Day Plan',
            'bw_limit_row'=>'All Day Plan',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'4.02GB',
            'access'=>1
        ];
    }

    function test_1_02_dataResetInGrace(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-SUNDAY EXCLUDED 100GB-1m',
                '2017-01-01 00:01:00'=>'authentication',
                '2017-01-01 22:35:00'=>'50gb',
                '2017-01-10 22:35:00'=>'5gb',
                '2017-01-25 22:45:00'=>'20gb',
                '2017-01-30 22:35:00'=>'20gb',
                '2017-01-31 22:46:00'=>'authentication',
                '2017-02-01 00:00:00'=>'authentication',
            ]);
        return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    }

    function prepare_1_02_dataResetInGrace(){
        $this->proper_responses['test_1_02_dataResetInGrace']=[
            'data_limit_row'=>'All Day Plan',
            'bw_limit_row'=>'All Day Plan',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'0.00B',
            'access'=>1
        ];
    }

    function test_05_02_dataConsumedInGrace(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-SUNDAY EXCLUDED 100GB-1m',
                '2017-01-01 00:01:00'=>'authentication',
                '2017-01-01 22:35:00'=>'50gb',
                '2017-01-10 22:35:00'=>'5gb',
                '2017-01-25 22:45:00'=>'20gb',
                '2017-01-30 22:35:00'=>'20gb',
                '2017-01-31 22:46:00'=>'authentication',
                '2017-02-01 00:00:00'=>'authentication',
                '2017-02-02 22:35:00'=>'5gb',
                '2017-02-04 08:35:00'=>'10gb',
                '2017-02-04 20:35:00'=>'10gb',
                '2017-02-05 00:00:00'=>'authentication',

            ]);
        return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    }

    function prepare_05_02_dataConsumedInGrace(){
        $this->proper_responses['test_05_02_dataConsumedInGrace']=[
            'data_limit_row'=>'All Day Plan',
            'bw_limit_row'=>'All Day Plan',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'25.00GB',
            'access'=>1
        ];
    }

    function test_07_02_ExpieredAfterGrace(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-SUNDAY EXCLUDED 100GB-1m',
                '2017-01-01 00:01:00'=>'authentication',
                '2017-01-01 22:35:00'=>'50gb',
                '2017-01-10 22:35:00'=>'5gb',
                '2017-01-25 22:45:00'=>'20gb',
                '2017-01-30 22:35:00'=>'20gb',
                '2017-01-31 22:46:00'=>'authentication',
                '2017-02-01 00:00:00'=>'authentication',
                '2017-02-02 22:35:00'=>'5gb',
                '2017-02-04 08:35:00'=>'10gb',
                '2017-02-04 20:35:00'=>'10gb',
                '2017-02-05 00:00:00'=>'25gb',
                '2017-02-07 00:00:00'=>'authentication',
            ]);
        return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    }

    function prepare_07_02_ExpieredAfterGrace(){
        $this->proper_responses['test_07_02_ExpieredAfterGrace']=[
            'data_limit_row'=>'',
            'bw_limit_row'=>'',
            'dl'=>null,
            'ul'=>null,
            'data_consumed'=>'0.00B',
            'access'=>0
        ];
    }

    function test_topUpPlan(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-SUNDAY EXCLUDED 100GB-1m',
                '2017-01-01 00:01:00'=>'authentication',
                '2017-01-11 00:00:00'=>'authentication',
                '2017-01-11 08:00:00'=>'10gb',
            ]);
        return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    }

    function prepare_topUpPlan(){
        $this->proper_responses['test_topUpPlan']=[
            'data_limit_row'=>'All Day Plan',
            'bw_limit_row'=>'All Day Plan',
            'dl'=>'10.00MB',
            'ul'=>'10.00MB',
            'data_consumed'=>'10.00GB',
            'access'=>1
        ];
    }

    function test_topUpPlanwithDataConsume(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-SUNDAY EXCLUDED 100GB-1m',
                '2017-01-01 00:01:00'=>'authentication',
                '2017-01-11 00:00:00'=>'authentication',
                '2017-01-11 08:00:00'=>'10gb',
                '2017-01-12 00:00:00'=>'40gb',
                '2017-01-13 00:00:00'=>'50gb',
            ]);
        return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    }

    function prepare_topUpPlanwithDataConsume(){
        $this->proper_responses['test_topUpPlanwithDataConsume']=[
            'data_limit_row'=>'All Day Plan',
            'bw_limit_row'=>'All Day Plan',
            'dl'=>'10.00MB',
            'ul'=>'10.00MB',
            'data_consumed'=>'100.00GB',
            'access'=>1
        ];
    }

    function test_topUpPlanwithSundayOffer(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-SUNDAY EXCLUDED 100GB-1m',
                '2017-01-01 00:01:00'=>'authentication',
                '2017-01-11 00:00:00'=>'authentication',
                '2017-01-11 08:00:00'=>'10gb',
                '2017-01-12 00:00:00'=>'40gb',
                '2017-01-13 00:00:00'=>'50gb',
                '2017-01-15 08:00:00'=>'10gb',
            ]);
        return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    }

    function prepare_topUpPlanwithSundayOffer(){
        $this->proper_responses['test_topUpPlanwithSundayOffer']=[
            'data_limit_row'=>'All Day Plan',
            'bw_limit_row'=>'All Day Plan',
            'dl'=>'10.00MB',
            'ul'=>'10.00MB',
            'data_consumed'=>'100.00GB',
            'access'=>1
        ];
    }

    function test_18_01afterTopUpPlanExpired(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-SUNDAY EXCLUDED 100GB-1m',
                '2017-01-01 00:01:00'=>'authentication',
                '2017-01-11 00:00:00'=>'authentication',
                '2017-01-11 08:00:00'=>'10gb',
                '2017-01-12 00:00:00'=>'40gb',
                '2017-01-13 00:00:00'=>'50gb',
                '2017-01-15 08:00:00'=>'10gb',
                '2017-01-18 08:00:00'=>'authentication',
            ]);
        return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    }

    function prepare_18_01afterTopUpPlanExpired(){
        $this->proper_responses['test_18_01afterTopUpPlanExpired']=[
            'data_limit_row'=>'All Day Plan',
            'bw_limit_row'=>'All Day Plan',
            'dl'=>'512.00KB',
            'ul'=>'512.00KB',
            'data_consumed'=>'100.00GB',
            'access'=>1
        ];
    }


}
