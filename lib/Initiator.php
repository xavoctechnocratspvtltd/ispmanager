<?php

namespace xavoc\ispmanager;

class Initiator extends \Controller_Addon {
    
    public $addon_name = 'xavoc_ispmanager';

    function setup_admin(){
        $this->routePages('xavoc_ispmanager');
        $this->addLocation(array('template'=>'templates','js'=>'templates/js','css'=>'templates/css'))
        ->setBaseURL('../shared/apps/xavoc/ispmanager/');

        $m = $this->app->top_menu->addMenu('ISP MANAGER');
        $m->addItem(['Users','icon'=>'fa fa-check-square-o'],'xavoc_ispmanager_user');
        $m->addItem(['Plans','icon'=>'fa fa-check-square-o'],'xavoc_ispmanager_plan');
        $m->addItem(['Topups','icon'=>'fa fa-check-square-o'],'xavoc_ispmanager_topup');
        $m->addItem(['Invoices','icon'=>'fa fa-check-square-o'],'xavoc_ispmanager_invoice');
        $m->addItem(['Up-Coming Invoice','icon'=>'fa fa-check-square-o'],'xavoc_ispmanager_upcominginvoice');
        $m->addItem(['Configuration','icon'=>'fa fa-cog'],'xavoc_ispmanager_configuration');
        $m->addItem(['test','icon'=>'fa fa-cog'],'xavoc_ispmanager_test');

        $this->addAppFunctions();

        $user = $this->add('xavoc\ispmanager\Model_User');
        // $this->app->addHook('beforeQSPSave',[$user,'updateQSPBeforeSave']);
        $this->app->addHook('invoice_paid',[$user,'invoicePaid']);
        return $this;
    }

    function setup_pre_frontend(){
        $this->routePages('xavoc_dm');
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
        $this->routePages('xavoc_prompt-web');
        $this->addLocation(array('template'=>'templates','js'=>'templates/js','css'=>'templates/css'))
        ->setBaseURL('./shared/apps/xavoc/ispmanager/');
        
        $this->addAppFunctions();
        
        $this->app->exportFrontEndTool('xavoc\ispmanager\Tool_HotspotLogin','ISPMANAGER');
        $this->app->exportFrontEndTool('xavoc\ispmanager\Tool_HotspotRegistration','ISPMANAGER');
        $this->app->exportFrontEndTool('xavoc\ispmanager\Tool_PurchasePlan','ISPMANAGER');
        $this->app->exportFrontEndTool('xavoc\ispmanager\Tool_PurchaseTopUp','ISPMANAGER');
        $this->app->exportFrontEndTool('xavoc\ispmanager\Tool_StaffPanel','ISPMANAGER');
        $this->app->exportFrontEndTool('xavoc\ispmanager\Tool_StaffMenuBar','ISPMANAGER');
        $this->app->exportFrontEndTool('xavoc\ispmanager\Tool_User_MenuBar','ISPMANAGER');
        $this->app->exportFrontEndTool('xavoc\ispmanager\Tool_User_DashBoard','ISPMANAGER');

        $user = $this->add('xavoc\ispmanager\Model_User');
        $this->app->addHook('invoice_paid',[$user,'invoicePaid']);
        
        return $this;
    }

}
