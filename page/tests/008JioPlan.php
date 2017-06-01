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

class page_tests_008JioPlan extends page_Tester {
	
	public $title='PerDay1GBjioPlan';
	
	public $proper_responses=[''];

    public $user;
    public $on_date;

    function init(){
        $this->user = $this->add('xavoc\ispmanager\Model_User')->loadBy('name','test user');
        parent::init();
    }

    function test_setplan_jio1GB(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-jio plan',
                '2017-01-01 08:01:00'=>'getdata'
            ]);
        return $r;
    }

    function prepare_setplan_jio1GB(){
        $this->proper_responses['test_setplan_jio1GB']=[
            [   
                'user'=>'Test User',
                'plan'=>'jio plan',
                'remark'=>'Main Plan',
                'data_limit'=>'1.00GB',
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
                'start_date'=>'2017-01-01 00:00:00',
                'end_date'=>'2017-02-01 00:00:00',
                'expire_date'=>'2017-02-06 00:00:00',
                'is_expired'=>0,
                'is_recurring'=>1,
                'reset_date'=>'2017-01-02 00:00:00',
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
        return [array_keys($this->proper_responses['test_setplan_jio1GB'][0])];
    }


    function test_jio01_01(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-jio plan',
                '2017-01-01 00:01:00'=>'authentication'
            ]);
        return $this->result($r);
    }

    function prepare_jio01_01(){
        $this->proper_responses['test_jio01_01']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'0.00B',
            'time_limit'=>0,
            'time_consumed'=>0,
            'access'=>1,
            'coa'=>1

        ];
    }

    function test_jioConsume01_01_1gb(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-jio plan',
                '2017-01-01 10:59:59'=>'authentication',
                '2017-01-01 18:35:00'=>'500mb',
            ]);
        return $this->result($r);
    }

    function prepare_jioConsume01_01_1gb(){
        $this->proper_responses['test_jioConsume01_01_1gb']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'500.00MB',
            'time_limit'=>0,
            'time_consumed'=>0,
            'access'=>1,
            'coa'=>0
        ];
    }

    function test_jiodataConsumed(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-jio plan',
                '2017-01-01 10:59:59'=>'authentication',
                '2017-01-01 18:35:00'=>'500mb',
                '2017-01-01 22:35:00'=>'524mb',
            ]);
        return $this->result($r);
    }

    function prepare_jiodataConsumed(){
        $this->proper_responses['test_jiodataConsumed']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>'512.00KB',
            'ul'=>'512.00KB',
            'data_consumed'=>'1.00GB',
            'time_limit'=>0,
            'time_consumed'=>0,
            'access'=>1,
            'coa'=>1
        ];
    }

    function test_jio01_02_dataResetInGrace(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-jio plan',
                '2017-01-01 10:59:59'=>'authentication',
                '2017-01-01 18:35:00'=>'500mb',
                '2017-01-01 22:35:00'=>'524mb',
                '2017-01-02 01:00:00'=>'authentication',
            ]);
        return $this->result($r);
    }

    function prepare_jio01_02_dataResetInGrace(){
        $this->proper_responses['test_jio01_02_dataResetInGrace']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'0.00B',
            'time_limit'=>0,
            'time_consumed'=>0,
            'access'=>1,
            'coa'=>0
        ];
    }

    // function test_jio01_02_dataConsumed(){
    //     $r = $this->process([
    //             '2017-01-01 00:00:00'=>'plan-jio plan',
    //             '2017-01-01 10:59:59'=>'authentication',
    //             '2017-01-01 18:35:00'=>'500mb',
    //             '2017-01-01 22:35:00'=>'524mb',
    //             '2017-01-02 01:10:00'=>'100mb',
                
    //         ]);
    //     return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    // }

    // function prepare_jio01_02_dataConsumed(){
    //     $this->proper_responses['test_jio01_02_dataConsumed']=[
    //         'data_limit_row'=>'Main Plan',
    //         'bw_limit_row'=>'Main Plan',
    //         'dl'=>'2.00MB',
    //         'ul'=>'2.00MB',
    //         'data_consumed'=>'100.00MB',
    //         'access'=>1
    //     ];
    // }

    function test_jiodataConsumedInGrace(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-jio plan',
                '2017-01-01 10:59:59'=>'authentication',
                '2017-01-01 18:35:00'=>'500mb',
                '2017-01-01 22:35:00'=>'524mb',
                '2017-01-02 01:10:00'=>'100mb',
                '2017-01-02 10:35:00'=>'100mb',
                '2017-01-02 18:00:00'=>'authentication',
                
            ]);
        return $this->result($r);
    }

    function prepare_jiodataConsumedInGrace(){
        $this->proper_responses['test_jiodataConsumedInGrace']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'200.00MB',
            'time_limit'=>0,
            'time_consumed'=>0,
            'access'=>1,
            'coa'=>0
        ];
    }

    function test_jiodataBeforeExpire(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-jio plan',
                '2017-01-01 10:59:59'=>'authentication',
                '2017-01-01 18:35:00'=>'500mb',
                '2017-01-01 22:35:00'=>'524mb',
                '2017-01-02 01:10:00'=>'100mb',
                '2017-01-02 10:35:00'=>'100mb',
                '2017-01-02 18:00:00'=>'authentication',
                '2017-02-02 00:00:00'=>'1gb',
                '2017-02-03 20:00:00'=>'1gb',
                // '2017-02-05 00:00:00'=>'authentication',
                
            ]);
        return $this->result($r);
    }

    function prepare_jiodataBeforeExpire(){
        $this->proper_responses['test_jiodataBeforeExpire']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>'512.00KB',
            'ul'=>'512.00KB',
            'data_consumed'=>'1.00GB',
            'time_limit'=>0,
            'time_consumed'=>0,
            'access'=>1,
            'coa'=>1
        ];
    }
    function test_jioExpieredAfterGrace(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-jio plan',
                '2017-01-01 10:59:59'=>'authentication',
                '2017-01-01 18:35:00'=>'500mb',
                '2017-01-01 22:35:00'=>'524mb',
                '2017-01-02 01:10:00'=>'100mb',
                '2017-01-02 10:35:00'=>'100mb',
                // '2017-01-02 18:00:00'=>'authentication',
                '2017-02-06 01:00:00'=>'authentication',
            ]);
        return $this->result($r);
    }

    function prepare_jioExpieredAfterGrace(){
        $this->proper_responses['test_jioExpieredAfterGrace']=[
            'data_limit_row'=>'',
            'bw_limit_row'=>'',
            'dl'=>null,
            'ul'=>null,
            'data_consumed'=>'0.00B',
            'time_limit'=>0,
            'time_consumed'=>0,
            'access'=>0,
            'coa'=>1
        ];
    }

}
