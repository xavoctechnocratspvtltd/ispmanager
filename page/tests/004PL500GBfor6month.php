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

class page_tests_004PL500GBfor6month extends page_Tester{
	public $title='PL-500GBfor6month';
	
	public $proper_responses=[''];

    public $user;
    public $on_date;
	
	function init(){
		 $this->user = $this->add('xavoc\ispmanager\Model_User')->loadBy('name','test user');
		parent::init();

	}

	function test_setplan($fields){
        // setted in init
        $this->user->setPlan('PL-500 GB for 6 month','2017-01-01',true);
        $model = $this->add('xavoc\ispmanager\Model_UserPlanAndTopup')
            ->addCondition('user_id',$this->user->id);
        $data=[];
        foreach ($model as $m) {
            $data[] =$m->data;    
        }

        return $this->filterColumns($data,$fields);
    }

    function prepare_setplan(){
        $this->proper_responses['test_setplan']=[
            [   
                'user'=>'Test User',
                'plan'=>'PL-500 GB for 6 month',
                'data_limit'=>'500.00GB',
                'download_limit'=>'1.00MB',
                'upload_limit'=>'1.00MB',
                'fup_download_limit'=>null,
                'fup_upload_limit'=>null,
                'accounting_download_ratio'=>'100',
                'accounting_upload_ratio'=>'100',
                'is_data_carry_forward'=>0,
                'start_time'=>null,
                'end_time'=>null,
                'start_date'=>'2017-01-01 00:00:00',
                'end_date'=>'2017-07-01 00:00:00',
                'expire_date'=>'2017-07-06 00:00:00',
                'is_expired'=>0,
                'is_recurring'=>0,
                'reset_date'=>'2017-07-01 00:00:00',
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
        return [array_keys($this->proper_responses['test_setplan'][0])];
    }

    function test_1_01(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-PL-500 GB for 6 month',
                '2017-01-01 00:01:00'=>'authentication'
            ]);
        return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    }

    function prepare_1_01(){
        $this->proper_responses['test_1_01']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>'1.00MB',
            'ul'=>'1.00MB',
            'data_consumed'=>'0.00B',
            'access'=>1
        ];
    }

    function test_10_01_20gb_dataConsume(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-PL-500 GB for 6 month',
                '2017-01-10 22:30:00'=>'authentication',
                '2017-01-10 22:35:00'=>'20gb',
            ]);
        return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    }

    function prepare_10_01_20gb_dataConsume(){
        $this->proper_responses['test_10_01_20gb_dataConsume']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>'1.00MB',
            'ul'=>'1.00MB',
            'data_consumed'=>'20.00GB',
            'access'=>1
        ];
    }

    function test_15_06_dataConsumed(){
        $r = $this->process([
                '2017-01-01 00:00:00'=>'plan-PL-500 GB for 6 month',
                '2017-01-10 22:30:00'=>'authentication',
                '2017-01-10 22:35:00'=>'50gb',
                '2017-02-12 22:35:00'=>'50gb',
                '2017-03-12 22:45:00'=>'200gb',
                '2017-04-25 22:35:00'=>'200gb',
                '2017-06-13 22:46:00'=>'authentication',
            ]);
        return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    }

    function prepare_15_06_dataConsumed(){
        $this->proper_responses['test_15_06_dataConsumed']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>null,
            'ul'=>null,
            'data_consumed'=>'500.00GB',
            'access'=>0
        ];
    }

    function test_1_07_dataResetInGrace(){
        $r = $this->process([
        		'2017-01-01 00:00:00'=>'plan-PL-500 GB for 6 month',
                '2017-01-10 22:30:00'=>'authentication',
                '2017-01-10 22:35:00'=>'50gb',
                '2017-02-12 22:35:00'=>'50gb',
                '2017-03-12 22:45:00'=>'200gb',
                '2017-04-25 22:35:00'=>'200gb',
                '2017-06-13 22:46:00'=>'authentication',
                '2017-07-01 00:00:00'=>'authentication',
            ]);
        return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    }

    function prepare_1_07_dataResetInGrace(){
        $this->proper_responses['test_1_07_dataResetInGrace']=[
            'data_limit_row'=>'Main Plan',
            'bw_limit_row'=>'Main Plan',
            'dl'=>'1.00MB',
            'ul'=>'1.00MB',
            'data_consumed'=>'0.00B',
            'access'=>1
        ];
    }

    // function test_05_07_dataConsumedInGrace(){
    //     $r = $this->process([
    //     		'2017-01-01 00:00:00'=>'plan-PL-500 GB for 6 month',
    //             '2017-01-10 22:30:00'=>'authentication',
    //             '2017-01-10 22:35:00'=>'5gb',
    //             '2017-02-12 22:35:00'=>'5gb',
    //             '2017-03-12 22:45:00'=>'20gb',
    //             '2017-04-25 22:35:00'=>'20gb',
    //             '2017-06-13 22:46:00'=>'authentication',
    //             '2017-07-01 00:00:00'=>'authentication',
    //             '2017-07-02 22:46:00'=>'40gb',
    //             '2017-07-03 22:46:00'=>'10gb',
    //             '2017-07-04 22:46:00'=>'authentication',
    //         ]);
    //     return ['data_limit_row'=>$r['result']['data_limit_row'],'bw_limit_row'=>$r['result']['bw_limit_row'],'dl'=>$r['result']['dl_limit'],'ul'=>$r['result']['ul_limit'],'data_consumed'=>$r['result']['data_consumed'],'access'=>$r['access']];
    // }

    // function prepare_05_06_dataConsumedInGrace(){
    //     $this->proper_responses['test_05_06_dataConsumedInGrace']=[
    //         'data_limit_row'=>'Main Plan',
    //         'bw_limit_row'=>'Main Plan',
    //         'dl'=>null,
    //         'ul'=>null,
    //         'data_consumed'=>'50.00GB',
    //         'access'=>0
    //     ];
    // }
}