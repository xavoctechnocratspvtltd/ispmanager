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

class page_tests_006Plan100GBUnlimited extends page_Tester {
	
	public $title='Plan100GBUnlimited';
	
	public $proper_responses=[''];

    public $user;
    public $on_date;

    function init(){
        $this->user = $this->add('xavoc\ispmanager\Model_User')->loadBy('name','test user');
        parent::init();
    }

    function test_setplan_unlimited($fields){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-unlimited 100GB-m',
                '2017-01-01 08:01:00'=>'getdata'
            ]);
        return $r;
    }

    function prepare_setplan_unlimited(){
        $this->proper_responses['test_setplan_unlimited']=[
            [   
                'user'=>'Test User',
                'plan'=>'unlimited 100GB-m',
                'data_limit'=>'100.00GB',
                'download_limit'=>'2.00MB',
                'upload_limit'=>'2.00MB',
                'fup_download_limit'=>'512.00KB',
                'fup_upload_limit'=>'512.00KB',
                'accounting_download_ratio'=>'100',
                'accounting_upload_ratio'=>'100',
                'is_data_carry_forward'=>0,
                'start_time'=>null,
                'end_time'=>null,
                'start_date'=>'2017-01-01 00:00:00',
                'end_date'=>'2017-02-01 00:00:00',
                'expire_date'=>'2017-02-06 00:00:00',
                'is_expired'=>0,
                'is_recurring'=>1,
                'reset_date'=>'2017-02-01 00:00:00',
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
        return [array_keys($this->proper_responses['test_setplan_unlimited'][0])];
    }


    function test_unlimitedMonth01_01(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-unlimited 100GB-m',
                '2017-01-01 00:01:00'=>'authentication'
            ]);
        return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    }

    function prepare_unlimitedMonth01_01(){
        $this->proper_responses['test_unlimitedMonth01_01']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'0.00B',
            'access'=>1
        ];
    }

    function test_unlimitedMonth15_01_60gb(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-unlimited 100GB-m',
                '2017-01-15 00:00:00'=>'authentication',
                '2017-01-15 22:35:00'=>'60gb',
            ]);
        return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    }

    function prepare_unlimitedMonth15_01_60gb(){
        $this->proper_responses['test_unlimitedMonth15_01_60gb']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'60.00GB',
            'access'=>1
        ];
    }

    function test_unlimitedMonth25_01_dataConsume(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-unlimited 100GB-m',
                '2017-01-15 00:00:00'=>'authentication',
                '2017-01-15 22:35:00'=>'60gb',
                '2017-01-20 22:45:00'=>'20gb',
                '2017-01-25 22:25:00'=>'20gb',
                '2017-01-25 22:35:00'=>'authentication',
            ]);
        return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    }

    function prepare_unlimitedMonth25_01_dataConsume(){
        $this->proper_responses['test_unlimitedMonth25_01_dataConsume']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>'512.00KB',
            'ul'=>'512.00KB',
            'data_consumed'=>'100.00GB',
            'access'=>1
        ];
    }

    function test_unlimitedMonth30_01_dataResetInGrace(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-unlimited 100GB-m',
                '2017-01-15 00:00:00'=>'authentication',
                '2017-01-15 22:35:00'=>'60gb',
                '2017-01-20 22:45:00'=>'20gb',
                '2017-01-25 22:25:00'=>'20gb',
                '2017-01-25 22:35:00'=>'authentication',
                '2017-01-30 22:50:00'=>'authentication',
            ]);
        return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    }

    function prepare_unlimitedMonth30_01_dataResetInGrace(){
        $this->proper_responses['test_unlimitedMonth30_01_dataResetInGrace']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>'512.00KB',
            'ul'=>'512.00KB',
            'data_consumed'=>'100.00GB',
            'access'=>1
        ];
    }

    function test_unlimitedMonth05_02_dataConsumedInGrace(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-unlimited 100GB-m',
                '2017-01-15 00:00:00'=>'authentication',
                '2017-01-15 22:35:00'=>'60gb',
                '2017-01-20 22:45:00'=>'20gb',
                '2017-01-25 22:25:00'=>'20gb',
                '2017-01-25 22:35:00'=>'authentication',
                '2017-01-30 22:50:00'=>'authentication',
                '2017-02-02 22:45:00'=>'20gb',
                '2017-02-04 22:25:00'=>'20gb',
                '2017-02-05 22:46:00'=>'authentication',
            ]);
        return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    }

    function prepare_unlimitedMonth05_02_dataConsumedInGrace(){
        $this->proper_responses['test_unlimitedMonth05_02_dataConsumedInGrace']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'40.00GB',
            'access'=>1
        ];
    }

    function test_unlimitedMonth07_02_ExpieredAfterGrace(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-unlimited 100GB-m',
                '2017-01-15 00:00:00'=>'authentication',
                '2017-01-15 22:35:00'=>'60gb',
                '2017-01-20 22:45:00'=>'20gb',
                '2017-01-25 22:25:00'=>'20gb',
                '2017-01-25 22:35:00'=>'authentication',
                '2017-01-30 22:50:00'=>'authentication',
                '2017-02-02 22:45:00'=>'20gb',
                '2017-02-04 22:25:00'=>'20gb',
                '2017-02-07 22:46:00'=>'authentication',
            ]);
        return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    }

    function prepare_unlimitedMonth07_02_ExpieredAfterGrace(){
        $this->proper_responses['test_unlimitedMonth07_02_ExpieredAfterGrace']=[
            'data_limit_row'=>'',
            'bw_limit_row'=>'',
            'dl'=>null,
            'ul'=>null,
            'data_consumed'=>'0.00B',
            'access'=>0
        ];
    }

}
