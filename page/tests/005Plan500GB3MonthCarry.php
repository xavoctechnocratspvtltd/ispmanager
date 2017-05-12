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

class page_tests_005Plan500GB3MonthCarry extends page_Tester {
	
	public $title='Plan500GB3Month';
	
	public $proper_responses=[''];

    public $user;
    public $on_date;

    function init(){
        $this->user = $this->add('xavoc\ispmanager\Model_User')->loadBy('name','test user');
        parent::init();
    }

    function test_setplan_simpleway($fields){
        $this->user->setPlan('PL-500 GB for 3 month data carry','2017-05-01',true);
        $model = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup')
            ->addCondition('user_id',$this->user->id);
        $data=[];
        foreach ($model as $m) {
            $data[] =$m->data;    
        }

        return $this->filterColumns($data,$fields);
    }

    function prepare_setplan_simpleway(){
        $this->proper_responses['test_setplan_simpleway']=[
            [   
                'user'=>'Test User',
                'plan'=>'PL-500 GB for 3 month data carry',
                'data_limit'=>'500.00GB',
                'download_limit'=>'2.00MB',
                'upload_limit'=>'2.00MB',
                'fup_download_limit'=>null,
                'fup_upload_limit'=>null,
                'accounting_download_ratio'=>'100',
                'accounting_upload_ratio'=>'100',
                'is_data_carry_forward'=>'once',
                'start_time'=>null,
                'end_time'=>null,
                'start_date'=>'2017-05-01 00:00:00',
                'end_date'=>'2017-08-01 00:00:00',
                'expire_date'=>'2017-08-06 00:00:00',
                'is_expired'=>0,
                'is_recurring'=>1,
                'reset_date'=>'2017-08-01 00:00:00',
                'data_reset_value'=>'3',
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
                'd31'=>1
            ]
        ];
        return [array_keys($this->proper_responses['test_setplan_simpleway'][0])];
    }


    function test_monthDate01_05(){
        $r = $this->process([
                '2017-05-01 00:00:00'=>'plan-PL-500 GB for 3 month data carry',
                '2017-05-01 00:01:00'=>'authentication'
            ]);
        return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    }

    function prepare_monthDate01_05(){
        $this->proper_responses['test_monthDate01_05']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'0.00B',
            'access'=>1
        ];
    }

    function test_monthDate20_05_70gb(){
        $r = $this->process([
                '2017-05-01 00:00:00'=>'plan-PL-500 GB for 3 month data carry',
                '2017-05-20 00:00:00'=>'authentication',
                '2017-05-20 22:35:00'=>'70gb',
            ]);
        return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    }

    function prepare_monthDate20_05_70gb(){
        $this->proper_responses['test_monthDate20_05_70gb']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'70.00GB',
            'access'=>1
        ];
    }

    function test_monthDate01_06_dataUsed(){
        $r = $this->process([
                '2017-05-01 00:00:00'=>'plan-PL-500 GB for 3 month data carry',
                '2017-05-20 00:00:00'=>'authentication',
                '2017-05-20 22:30:00'=>'70gb',
                '2017-05-30 22:45:00'=>'80gb',
                '2017-06-01 22:25:00'=>'20gb',
                '2017-06-01 22:35:00'=>'authentication',
            ]);
        return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    }

    function prepare_monthDate01_06_dataUsed(){
        $this->proper_responses['test_monthDate01_06_dataUsed']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'170.00GB',
            'access'=>1
        ];
    }

    function test_monthDate01_07_dataConsume(){
        $r = $this->process([
                '2017-05-01 00:00:00'=>'plan-PL-500 GB for 3 month data carry',
                '2017-05-20 00:00:00'=>'authentication',
                '2017-05-20 22:30:00'=>'70gb',
                '2017-05-30 22:45:00'=>'80gb',
                '2017-06-01 22:25:00'=>'20gb',
                '2017-06-01 22:35:00'=>'authentication',
                '2017-06-10 22:35:00'=>'30gb',
                '2017-06-18 22:35:00'=>'40gb',
                '2017-06-20 22:50:00'=>'10gb',
                '2017-06-28 22:45:00'=>'60gb',
                '2017-06-30 22:45:00'=>'25gb',
                '2017-07-01 22:45:00'=>'authentication',
            ]);
        return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    }

    function prepare_monthDate01_07_dataConsume(){
        $this->proper_responses['test_monthDate01_07_dataConsume']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'335.00GB',
            'access'=>1
        ];
    }

    function test_monthDate01_08_dataConsumed(){
        $r = $this->process([
                '2017-05-01 00:00:00'=>'plan-PL-500 GB for 3 month data carry',
                '2017-05-20 00:00:00'=>'authentication',
                '2017-05-20 22:30:00'=>'70gb',
                '2017-05-30 22:45:00'=>'80gb',
                '2017-06-01 22:25:00'=>'20gb',
                '2017-06-01 22:35:00'=>'authentication',
                '2017-06-10 22:35:00'=>'30gb',
                '2017-06-18 22:35:00'=>'40gb',
                '2017-06-20 22:50:00'=>'10gb',
                '2017-06-28 22:45:00'=>'60gb',
                '2017-06-30 22:45:00'=>'25gb',
                '2017-07-01 22:45:00'=>'authentication',
                '2017-07-15 22:35:00'=>'50gb',
                '2017-07-28 22:35:00'=>'60gb',
                '2017-07-30 22:35:00'=>'10gb',
            ]);
        return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    }

    function prepare_monthDate01_08_dataConsumed(){
        $this->proper_responses['test_monthDate01_08_dataConsumed']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'455.00GB',
            'access'=>1
        ];
    }

    function test_monthDate01_08_dataResetInGrace(){
        $r = $this->process([
                '2017-05-01 00:00:00'=>'plan-PL-500 GB for 3 month data carry',
                '2017-05-20 00:00:00'=>'authentication',
                '2017-05-20 22:30:00'=>'70gb',
                '2017-05-30 22:45:00'=>'80gb',
                '2017-06-01 22:25:00'=>'20gb',
                '2017-06-01 22:35:00'=>'authentication',
                '2017-06-10 22:35:00'=>'30gb',
                '2017-06-18 22:35:00'=>'40gb',
                '2017-06-20 22:35:00'=>'20gb',
                '2017-06-20 22:50:00'=>'10gb',
                '2017-06-28 22:45:00'=>'60gb',
                '2017-06-30 22:45:00'=>'25gb',
                '2017-07-01 22:45:00'=>'authentication',
                '2017-07-15 22:35:00'=>'50gb',
                '2017-07-28 22:35:00'=>'60gb',
                '2017-07-30 22:35:00'=>'10gb',
                '2017-08-01 22:50:00'=>'authentication',
            ]);
        return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    }

    function prepare_monthDate01_08_dataResetInGrace(){
        $this->proper_responses['test_monthDate01_08_dataResetInGrace']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'0.00B',
            'access'=>1
        ];
    }

    function test_monthDate05_08_dataConsumedInGrace(){
        $r = $this->process([
                '2017-05-01 00:00:00'=>'plan-PL-500 GB for 3 month data carry',
                '2017-05-20 00:00:00'=>'authentication',
                '2017-05-20 22:30:00'=>'70gb',
                '2017-05-30 22:45:00'=>'80gb',
                '2017-06-01 22:25:00'=>'20gb',
                '2017-06-01 22:35:00'=>'authentication',
                '2017-06-10 22:35:00'=>'30gb',
                '2017-06-18 22:35:00'=>'40gb',
                '2017-06-20 22:35:00'=>'20gb',
                '2017-06-20 22:50:00'=>'10gb',
                '2017-06-28 22:45:00'=>'60gb',
                '2017-06-30 22:45:00'=>'25gb',
                '2017-07-01 22:45:00'=>'authentication',
                '2017-07-15 22:35:00'=>'50gb',
                '2017-07-28 22:35:00'=>'60gb',
                '2017-07-30 22:35:00'=>'10gb',
                '2017-08-01 22:50:00'=>'authentication',
                '2017-08-02 22:46:00'=>'100gb',
                '2017-08-03 22:46:00'=>'50gb',
                '2017-08-05 22:46:00'=>'authentication',
            ]);
        return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    }

    function prepare_monthDate05_08_dataConsumedInGrace(){
        $this->proper_responses['test_monthDate05_08_dataConsumedInGrace']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>'2.00MB',
            'ul'=>'2.00MB',
            'data_consumed'=>'150.00GB',
            'access'=>1
        ];
    }

    function test_monthDate07_08_ExpieredAfterGrace(){
        $r = $this->process([
                '2017-05-01 00:00:00'=>'plan-PL-500 GB for 3 month data carry',
                '2017-05-20 00:00:00'=>'authentication',
                '2017-05-20 22:30:00'=>'70gb',
                '2017-05-30 22:45:00'=>'80gb',
                '2017-06-01 22:25:00'=>'20gb',
                '2017-06-01 22:35:00'=>'authentication',
                '2017-06-10 22:35:00'=>'30gb',
                '2017-06-18 22:35:00'=>'40gb',
                '2017-06-20 22:35:00'=>'20gb',
                '2017-06-20 22:50:00'=>'10gb',
                '2017-06-28 22:45:00'=>'60gb',
                '2017-06-30 22:45:00'=>'25gb',
                '2017-07-01 22:45:00'=>'authentication',
                '2017-07-15 22:35:00'=>'50gb',
                '2017-07-28 22:35:00'=>'70gb',
                '2017-07-30 22:35:00'=>'25gb',
                '2017-08-01 22:50:00'=>'authentication',
                '2017-08-02 22:46:00'=>'100gb',
                '2017-08-03 22:46:00'=>'50gb',
                '2017-08-07 22:46:00'=>'authentication',
            ]);
        return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    }

    function prepare_monthDate07_08_ExpieredAfterGrace(){
        $this->proper_responses['test_monthDate07_08_ExpieredAfterGrace']=[
            'data_limit_row'=>'',
            'bw_limit_row'=>'',
            'dl'=>null,
            'ul'=>null,
            'data_consumed'=>'0.00B',
            'access'=>0
        ];
    }


}
