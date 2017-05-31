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

class page_tests_002PL50M extends page_Tester {
	
	public $title='SimpleDataPlan';
	
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
                '2017-05-01 00:00:00'=>'plan-PL-50-M','2017-05-01',
                '2017-05-25 00:00:00'=>'top-Top-After25-5GB-4MB-HighSpeed',
                '2017-05-27 08:01:00'=>'getdata'
            ]);
        return $r;
    }

    function prepare_setplan(){
        $this->proper_responses['test_setplan']=[
            [   
                'user'=>'Test User',
                'plan'=>'PL-50-M',
                'data_limit'=>'50.00GB',
                'download_limit'=>'1.00MB',
                'upload_limit'=>'1.00MB',
                'fup_download_limit'=>null,
                'fup_upload_limit'=>null,
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
                'data_reset_value'=>'1',
                'data_reset_mode'=>'months',
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
                'plan'=>'Top-After25-5GB-4MB-HighSpeed',
                'remark'=>'Main Topup',
                'data_limit'=>'5.00GB',
                'download_limit'=>'4.00MB',
                'upload_limit'=>'4.00MB',
                'fup_download_limit'=>null,
                'fup_upload_limit'=>null,
                'accounting_download_ratio'=>'100',
                'accounting_upload_ratio'=>'100',
                'start_date'=>'2017-05-25 00:00:00',
                'end_date'=>'2017-05-29 00:00:00',
                'expire_date'=>'2017-05-29 00:00:00',
                'is_expired'=>0,
                'is_recurring'=>0,
                'is_data_carry_forward'=>'none',
                'start_time'=>null,
                'end_time'=>null,
                'reset_date'=>"2017-05-29 00:00:00",
                'data_reset_value'=>4,
                'data_reset_mode'=>'days',
                'is_topup'=>1,
                'sun'=>1,
                'mon'=>1,
                'tue'=>1,
                'wed'=>1,
                'thu'=>1,
                'fri'=>1,
                'sat'=>1,
                'd01'=>0,
                'd02'=>0,
                'd03'=>0,
                'd04'=>0,
                'd05'=>0,
                'd06'=>0,
                'd07'=>0,
                'd08'=>0,
                'd09'=>0,
                'd10'=>0,
                'd11'=>0,
                'd12'=>0,
                'd13'=>0,
                'd14'=>0,
                'd15'=>0,
                'd16'=>0,
                'd17'=>0,
                'd18'=>0,
                'd19'=>0,
                'd20'=>0,
                'd21'=>0,
                'd22'=>0,
                'd23'=>0,
                'd24'=>0,
                'd25'=>1,
                'd26'=>1,
                'd27'=>1,
                'd28'=>1,
                'd29'=>0,
                'd30'=>0,
                'd31'=>0,
                'treat_fup_as_dl_for_last_limit_row'=>0
            ],
        ];
    }


    function test_1_05(){
        $r = $this->process([
                '2017-05-01 00:00:00'=>'plan-PL-50-M',
                '2017-05-01 00:01:00'=>'authentication'
            ]);
        return $this->result($r);
    }

    function prepare_1_05(){
        $this->proper_responses['test_1_05']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>'1.00MB',
            'ul'=>'1.00MB',
            'data_consumed'=>'0.00B',
            'access'=>1,
            'coa' => 1
        ];
    }

    function test_10_05_10gb_dataConsume(){
        $r = $this->process([
                '2017-05-01 00:00:00'=>'plan-PL-50-M',
                '2017-05-10 22:30:00'=>'authentication',
                '2017-05-10 22:35:00'=>'10gb/360',
            ]);
        return $this->result($r);
    }

    function prepare_10_05_10gb_dataConsume(){
        $this->proper_responses['test_10_05_10gb_dataConsume']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>'1.00MB',
            'ul'=>'1.00MB',
            'data_consumed'=>'10.00GB',
            'access'=>1,
            'coa' => false
        ];
    }

    function test_15_05_dataConsumed(){
        $r = $this->process([
                '2017-05-01 00:00:00'=>'plan-PL-50-M',
                '2017-05-10 22:30:00'=>'authentication',
                '2017-05-10 22:35:00'=>'10gb',
                '2017-05-12 22:35:00'=>'20gb',
                '2017-05-13 22:35:00'=>'19gb',
                '2017-05-13 22:40:00'=>'1gb',
                '2017-05-13 22:45:00'=>'1mb',
                '2017-05-13 22:46:00'=>'authentication',
            ]);
        return $this->result($r);
    }

    function prepare_15_05_dataConsumed(){
        $this->proper_responses['test_15_05_dataConsumed']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>null,
            'ul'=>null,
            'data_consumed'=>'50.00GB',
            'access'=>0,
            'coa' => 1
        ];
    }

    function test_1_06_dataResetInGrace(){
        $r = $this->process([
                '2017-05-01 00:00:00'=>'plan-PL-50-M',
                '2017-05-10 22:30:00'=>'authentication',
                '2017-05-10 22:35:00'=>'10gb',
                '2017-05-12 22:35:00'=>'20gb',
                '2017-05-13 22:35:00'=>'19gb',
                '2017-05-13 22:40:00'=>'1gb',
                '2017-05-13 22:45:00'=>'1mb',
                '2017-05-13 22:46:00'=>'authentication',
                '2017-06-01 22:46:00'=>'authentication',
            ]);
        return $this->result($r);
    }

    function prepare_1_06_dataResetInGrace(){
        $this->proper_responses['test_1_06_dataResetInGrace']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>'1.00MB',
            'ul'=>'1.00MB',
            'data_consumed'=>'0.00B',
            'access'=>1,
            'coa' => 1
        ];
    }

    function test_05_06_dataConsumedInGrace(){
        $r = $this->process([
                '2017-05-01 00:00:00'=>'plan-PL-50-M',
                '2017-05-10 22:30:00'=>'authentication',
                '2017-05-10 22:35:00'=>'10gb',
                '2017-05-12 22:35:00'=>'20gb',
                '2017-05-13 22:35:00'=>'19gb',
                '2017-05-13 22:40:00'=>'1gb',
                '2017-05-13 22:45:00'=>'1mb',
                '2017-05-13 22:46:00'=>'authentication',
                '2017-06-01 22:46:00'=>'authentication',
                '2017-06-02 22:46:00'=>'40gb',
                '2017-06-03 22:46:00'=>'10gb',
                '2017-06-04 22:46:00'=>'authentication',
            ]);
        return $this->result($r);
    }

    function prepare_05_06_dataConsumedInGrace(){
        $this->proper_responses['test_05_06_dataConsumedInGrace']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>null,
            'ul'=>null,
            'data_consumed'=>'50.00GB',
            'access'=>0,
            'coa' => 1
        ];
    }

    function test_07_06_ExpieredAfterGrace(){
        $r = $this->process([
                '2017-05-01 00:00:00'=>'plan-PL-50-M',
                '2017-05-10 22:30:00'=>'authentication',
                '2017-05-10 22:35:00'=>'10gb',
                '2017-05-12 22:35:00'=>'20gb',
                '2017-05-13 22:35:00'=>'19gb',
                '2017-05-13 22:40:00'=>'1gb',
                '2017-05-13 22:45:00'=>'1mb',
                '2017-05-13 22:46:00'=>'authentication',
                '2017-06-01 22:46:00'=>'authentication',
                '2017-06-03 22:46:00'=>'10gb',
                '2017-06-07 22:46:00'=>'authentication',
            ]);
        return $this->result($r);
    }

    function prepare_07_06_ExpieredAfterGrace(){
        $this->proper_responses['test_07_06_ExpieredAfterGrace']=[
            'data_limit_row'=>'',
            'bw_limit_row'=>'',
            'dl'=>null,
            'ul'=>null,
            'data_consumed'=>'0.00B',
            'access'=>0,
            'coa' => 1
        ];
    }


}
