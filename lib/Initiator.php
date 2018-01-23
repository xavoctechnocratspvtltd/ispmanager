<?php

namespace xavoc\ispmanager;

class Initiator extends \Controller_Addon {
    
    public $addon_name = 'xavoc_ispmanager';

    function setup_admin(){
        $this->routePages('xavoc_ispmanager');
        $this->addLocation(array('template'=>'templates','js'=>'templates/js','css'=>'templates/css'))
        ->setBaseURL('../shared/apps/xavoc/ispmanager/');

        $m = $this->app->top_menu->addMenu('CAF');
            $m->addItem(['Lead Category','icon'=>'fa fa-check-square-o'],'xepan_marketing_marketingcategory');
            $m->addItem(['Lead','icon'=>'fa fa-users'],'xavoc_ispmanager_lead');
            $m->addItem(['Installation Due','icon'=>'fa fa-users'],'xavoc_ispmanager_lead_installation');
            $m->addItem(['Installed','icon'=>'fa fa-users'],'xavoc_ispmanager_lead_installed');
            $m->addItem(['Active User','icon'=>'fa fa-users'],'xavoc_ispmanager_user');
            $m->addItem(['All Lead','icon'=>'fa fa-users'],'xavoc_ispmanager_lead_all');

        $m = $this->app->top_menu->addMenu('ISP MANAGER');
        // $m->addItem(['Leads','icon'=>'fa fa-users'],'xavoc_ispmanager_lead');
        $m->addItem(['Users','icon'=>'fa fa-check-square-o'],'xavoc_ispmanager_user');
        $m->addItem(['Plans','icon'=>'fa fa-check-square-o'],'xavoc_ispmanager_plan');
        $m->addItem(['Topups','icon'=>'fa fa-check-square-o'],'xavoc_ispmanager_topup');
        $m->addItem(['Invoices','icon'=>'fa fa-check-square-o'],'xavoc_ispmanager_invoice');
        $m->addItem(['Up-Coming Invoice','icon'=>'fa fa-check-square-o'],'xavoc_ispmanager_upcominginvoice');
        $m->addItem(['Microtik Routers','icon'=>'fa fa-check-square-o'],'xavoc_ispmanager_client');
        $m->addItem(['Configuration','icon'=>'fa fa-cog'],'xavoc_ispmanager_configuration');
        $m->addItem(['Log','icon'=>'fa fa-user'],'xavoc_ispmanager_log');
        $m->addItem(['Device Management','icon'=>'fa fa-user'],'xavoc_ispmanager_device');
        $m->addItem(['Employee Payment Collection','icon'=>'fa fa-users'],'xavoc_ispmanager_employeepaymentcollection');

        $m = $this->app->top_menu->addMenu('channel');
        $m->addItem(['channel Mgnt','icon'=>'fa fa-cog'],'xavoc_ispmanager_channel_channel');
        $m->addItem(['Plan','icon'=>'fa fa-cog'],'xavoc_ispmanager_channel_plan');
        $m->addItem(['Lead','icon'=>'fa fa-cog'],'xavoc_ispmanager_channel_lead');
        $m->addItem(['ISP User','icon'=>'fa fa-cog'],'xavoc_ispmanager_channel_user');
        $m->addItem(['Commission Mgnt','icon'=>'fa fa-cog'],'xavoc_ispmanager_channel_commission');
        $m->addItem(['Invoice','icon'=>'fa fa-cog'],'xavoc_ispmanager_channel_invoice');
        $m->addItem(['Payment Collection','icon'=>'fa fa-cog'],'xavoc_ispmanager_channel_paymentcollection');
        $m->addItem(['Ticket','icon'=>'fa fa-cog'],'xavoc_ispmanager_channel_ticket');

        $this->addAppFunctions();

        $user = $this->add('xavoc\ispmanager\Model_User');
        // $this->app->addHook('beforeQSPSave',[$user,'updateQSPBeforeSave']);
        $this->app->addHook('invoice_paid',[$user,'invoicePaid']);

        $this->app->addHook('new_lead_added',function($app,$model){
            
            if($model['status'] == "Active" AND $model['assign_to_id'] > 0){
                $lead = $this->add('xavoc\ispmanager\Model_Lead')->load($model['id']);
                $lead->assign($model['assign_to_id']);
            }
        });

        $this->app->addHook('entity_collection',[$this,'exportEntities']);

        return $this;
    }

    function exportEntities($app,&$array){
        $array['ispmanager_plan'] = ['caption'=>'Plan','type'=>'DropDown','model'=>'xavoc\ispmanager\Model_Plan'];
        $array['ispmanager_user'] = ['caption'=>'ISP User','type'=>'DropDown','model'=>'xavoc\ispmanager\Model_User'];
        $array['ispmanager_Lead'] = ['caption'=>'ISP User','type'=>'DropDown','model'=>'xavoc\ispmanager\Model_Lead'];
        
    }

    function setup_pre_frontend(){
        $this->routePages('xavoc_ispmanager');
        $this->addLocation(array('template'=>'templates','js'=>'templates/js','css'=>'templates/css'))
        ->setBaseURL('./shared/apps/xavoc/mlm/');

        return $this;
    }

    function addAppFunctions(){
        
        $this->app->addMethod('byte2human',function($app,$bytes, $decimals = 2){
            $size = array('B','KB','MB','GB','TB','PB','EB','ZB','YB');
            $factor = floor((strlen($bytes) - 1) / 3);
            return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
        });

        $this->app->addMethod('human2byte',function($app,$value){
              return preg_replace_callback('/^\s*(\d*\.?\d+)\s*(?:([kmgtpy]?)b?)?\s*$/i', function ($m) {
                switch (strtolower($m[2])) {
                  case 'y': $m[1] *= 1024;
                  case 'p': $m[1] *= 1024;
                  case 't': $m[1] *= 1024;
                  case 'g': $m[1] *= 1024;
                  case 'm': $m[1] *= 1024;
                  case 'k': $m[1] *= 1024;
                }
                return $m[1];
              }, $value);
        });
    }

    function setup_frontend(){
        $this->routePages('xavoc_ispmanager');
        $this->addLocation(array('template'=>'templates','js'=>'templates/js','css'=>'templates/css'))
        ->setBaseURL('./shared/apps/xavoc/ispmanager/');
        
        $this->addAppFunctions();
        
        $this->app->exportFrontEndTool('xavoc\ispmanager\Tool_HotspotLogin','ISPMANAGER');
        $this->app->exportFrontEndTool('xavoc\ispmanager\Tool_HotspotRegistration','ISPMANAGER');
        $this->app->exportFrontEndTool('xavoc\ispmanager\Tool_HotspotForgotpassword','ISPMANAGER');
        $this->app->exportFrontEndTool('xavoc\ispmanager\Tool_PurchasePlan','ISPMANAGER');
        $this->app->exportFrontEndTool('xavoc\ispmanager\Tool_PurchaseTopUp','ISPMANAGER');
        $this->app->exportFrontEndTool('xavoc\ispmanager\Tool_Staff_Panel','ISPMANAGER');
        $this->app->exportFrontEndTool('xavoc\ispmanager\Tool_Staff_MenuBar','ISPMANAGER');
        $this->app->exportFrontEndTool('xavoc\ispmanager\Tool_User_MenuBar','ISPMANAGER');
        $this->app->exportFrontEndTool('xavoc\ispmanager\Tool_User_DashBoard','ISPMANAGER');
        $this->app->exportFrontEndTool('xavoc\ispmanager\Tool_User_Profile','ISPMANAGER');
        $this->app->exportFrontEndTool('xavoc\ispmanager\Tool_CustomerRegistration','ISPMANAGER');
        $this->app->exportFrontEndTool('xavoc\ispmanager\Tool_Staff_MyLead','ISPMANAGER');
        $this->app->exportFrontEndTool('xavoc\ispmanager\Tool_Staff_PaymentReceived','ISPMANAGER');
        $this->app->exportFrontEndTool('xavoc\ispmanager\Tool_Channel_MenuBar','ISPMANAGER');

        $user = $this->add('xavoc\ispmanager\Model_User');
        $this->app->addHook('invoice_paid',[$user,'invoicePaid']);
        
        // cron job 
        $this->app->addHook('cron_executor',function($app){
            
            $now = \DateTime::createFromFormat('Y-m-d H:i:s', $this->app->now);
            $job = new \Cron\Job\ShellJob();
            $job->setSchedule(new \Cron\Schedule\CrontabSchedule('0 0 * * *'));
            if(!$job->getSchedule() || $job->getSchedule()->valid($now)){
                echo " Executing Condition Reset<br/>";
                $this->add('xavoc\ispmanager\Controller_ResetUserPlanAndTopup')->run();
            }

        });

        return $this;
    }

}
