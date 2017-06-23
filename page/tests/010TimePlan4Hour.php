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

class page_tests_010TimePlan4Hour extends page_Tester {
	
	public $title='4 Hour Time Plan';
	
	public $proper_responses=[''];

    public $user;
    public $on_date;

    function init(){
        $this->user = $this->add('xavoc\ispmanager\Model_User')->loadBy('name','test user');
        parent::init();
    }

    function test_setplan(){
        $r = $this->process([
                '2017-05-01 00:00:00'=>'plan-PL-4HOUR-Time',
                '2017-05-01 08:01:00'=>'getdata'
            ]);
        return $r;
    }

    function prepare_setplan(){
        $this->proper_responses['test_setplan']=[
                [
                'user'=>'Test User',
                'plan'=>'PL-4HOUR-Time',
                'remark'=>'Time Plan',
                'data_limit'=>'1.00GB',
                'time_limit'=>'14400',
                'download_limit'=>'4.00MB',
                'upload_limit'=>'4.00MB',
                'fup_download_limit'=>null,
                'fup_upload_limit'=>null,
                'accounting_download_ratio'=>'100',
                'accounting_upload_ratio'=>'100',
                'is_data_carry_forward'=>'none',
                'start_time'=>null,
                'end_time'=>null,
                'start_date'=>'2017-05-01 00:00:00',
                'end_date'=>'2017-06-01 00:00:00',
                'expire_date'=>'2017-06-06 00:00:00',
                'is_expired'=>0,
                'is_recurring'=>0,
                'reset_date'=>'2017-05-02 00:00:00',
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

    function test_01_DataAndTimeUsed(){
            $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-PL-4HOUR-Time',
                '2017-01-01 00:01:00'=>'authentication',
                '2017-01-01 00:02:00'=>'200mb/3600',
                '2017-01-01 01:02:00'=>'400mb/3600',
                '2017-01-01 03:02:00'=>'200mb/3600',
                '2017-01-01 06:02:00'=>'authentication',
            ]);
        return $this->result($r);
    }

    function prepare_01_DataAndTimeUsed(){
        $this->proper_responses['test_01_DataAndTimeUsed']=[
            'data_limit_row'=>'Time Plan',
            'bw_limit_row'=>'Time Plan',
            'dl'=>'4.00MB',
            'ul'=>'4.00MB',
            'data_consumed'=>'800.00MB',
            'time_limit'=>'14400',
            'time_consumed'=>'10800',
            'access'=>1,
            'coa' => 0
        ];
    }

    function test_01_TimeConsumed(){
            $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-PL-4HOUR-Time',
                '2017-01-01 00:01:00'=>'authentication',
                '2017-01-01 00:02:00'=>'100mb/3600',
                '2017-01-01 01:02:00'=>'200mb/3600',
                '2017-01-01 03:02:00'=>'200mb/3600',
                '2017-01-01 04:02:00'=>'200mb/3500',
                '2017-01-01 05:02:00'=>'10mb/110',
            ]);
        return $this->result($r);
    }

    function prepare_01_TimeConsumed(){
        $this->proper_responses['test_01_TimeConsumed']=[
            'data_limit_row'=>'Time Plan',
            'bw_limit_row'=>'Time Plan',
            'dl'=>'4.00MB',
            'ul'=>'4.00MB',
            'data_consumed'=>'710.00MB',
            'time_limit'=>'14400',
            'time_consumed'=>'14410',
            'access'=>0,
            'coa' => 1
        ];
    }

    function test_01_DataConsumed(){
            $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-PL-4HOUR-Time',
                '2017-01-01 00:01:00'=>'authentication',
                '2017-01-01 00:02:00'=>'0.3gb/600',
                '2017-01-01 01:02:00'=>'0.3gb/600',
                '2017-01-01 03:02:00'=>'0.3gb/600',
                '2017-01-01 04:02:00'=>'0.4gb/600',
            ]);
        return $this->result($r);
    }

    function prepare_01_DataConsumed(){
        $this->proper_responses['test_01_DataConsumed']=[
            'data_limit_row'=>'Time Plan',
            'bw_limit_row'=>'Time Plan',
            'dl'=>'',
            'ul'=>'',
            'data_consumed'=>'1.30GB',
            'time_limit'=>'14400',
            'time_consumed'=>'2400',
            'access'=>0,
            'coa' => 1
        ];
    }


}
