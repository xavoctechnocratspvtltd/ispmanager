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

class page_tests_007SundayHighSpeed extends page_Tester {
	
	public $title='SundayHighSpeed';
	
	public $proper_responses=[''];

    public $user;
    public $on_date;

    function init(){
        $this->user = $this->add('xavoc\ispmanager\Model_User')->loadBy('name','test user');
        parent::init();
    }

    
    function test_setplan_allDayAndSunday(){
         $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-High Speed 100GB-2mb',
                '2017-01-01 08:01:00'=>'getdata'
            ]);
        return $r;

    }

    function prepare_setplan_allDayAndSunday(){
        $this->proper_responses['test_setplan_allDayAndSunday']=[
            [   
                'user'=>'Test User',
                'plan'=>'High Speed 100GB-2mb',
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
                'plan'=>'High Speed 100GB-2mb',
                'data_limit'=>'2.00GB',
                'download_limit'=>'2.00MB',
                'upload_limit'=>'2.00MB',
                'fup_download_limit'=>'1.00MB',
                'fup_upload_limit'=>'1.00MB',
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
                'reset_date'=>'2017-01-02 00:00:00',
                'data_reset_value'=>'1',
                'data_reset_mode'=>'days',
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
                'd31'=>1,
                'treat_fup_as_dl_for_last_limit_row'=>1
            ]
        ];
        return [array_keys($this->proper_responses['test_setplan_allDayAndSunday'][0])];
    }

     function test_sundayEffectiveRow(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-High Speed 100GB-2mb',
                '2017-01-01 00:01:00'=>'authentication'
            ]);
        return $this->result($r);
    }

    function prepare_sundayEffectiveRow(){
        $this->proper_responses['test_sundayEffectiveRow']=[
            'data_limit_row'=>'Sunday 2GB Extra',
            'bw_limit_row'=>'Sunday 2GB Extra',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'0.00B',
            'access'=>1,
            'coa' => false
        ];
    }

    function test_sundayUnderDataLimit(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-High Speed 100GB-2mb',
                '2017-01-01 00:01:00'=>'authentication',
                '2017-01-01 10:01:00'=>'1.5gb'
            ]);
        return $this->result($r);
    }

    function prepare_sundayUnderDataLimit(){
        $this->proper_responses['test_sundayUnderDataLimit'] = [
            'data_limit_row'=>'Sunday 2GB Extra',
            'bw_limit_row'=>'Sunday 2GB Extra',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'1.50GB',
            'access'=>1,
            'coa'=>0
        ];
    }

    function test_sundayDataLimitCrossed(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-High Speed 100GB-2mb',
                '2017-01-01 00:01:00'=>'authentication',
                '2017-01-01 10:01:00'=>'1.5gb',
                '2017-01-01 13:03:00'=>'0.6gb',
                // '2017-01-01 14:00:00'=>'1.0gb'
            ]);
        return $this->result($r);
    }

    function prepare_sundayDataLimitCrossed(){
        $this->proper_responses['test_sundayDataLimitCrossed'] = [
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Sunday 2GB Extra',
            'dl'=>'1.00MB',
            'ul'=>'1.00MB',
            'data_consumed'=>'2.10GB',
            'access'=>1,
            'coa'=>1,
        ];
    }

    function test_allDayEffectiveRow(){
        $r = $this->process([
                '2017-01-02 00:00:00'=>'plan-High Speed 100GB-2mb',
                '2017-01-02 08:01:00'=>'authentication'
            ]);
        return $this->result($r);
    }

    function prepare_allDayEffectiveRow(){
        $this->proper_responses['test_allDayEffectiveRow']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'0.00B',
            'access'=>1,
            'coa' => false
        ];
    }

    function test_MaintainAllDayData(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-High Speed 100GB-2mb',
                '2017-01-01 07:50:00'=>'authentication',
                '2017-01-01 07:55:00'=>'1gb',
                '2017-01-01 08:00:00'=>'1gb',
                '2017-01-02 08:10:00'=>'20gb',
                '2017-01-03 08:15:00'=>'5gb',
                '2017-01-04 08:10:00'=>'login',
            ]);
        return $this->result($r);
    }

    function prepare_MaintainAllDayData(){
        $this->proper_responses['test_MaintainAllDayData']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'25.00GB',
            'access'=>1,
            'coa' => false
        ];
    }
    
}
