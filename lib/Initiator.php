<?php

namespace xavoc\ispmanager;

class Initiator extends \Controller_Addon {
    
    public $addon_name = 'xavoc_ispmanager';

    function setup_admin(){

        if(!$this->add('xepan\base\Controller_License')->check(__NAMESPACE__)) return;

        $this->routePages('xavoc_ispmanager');
        $this->addLocation(array('template'=>'templates','js'=>'templates/js','css'=>'templates/css'))
            ->setBaseURL('../shared/apps/xavoc/ispmanager/');

        $this->app->js(true)->_css('adminstyle');

        if($this->app->is_admin && !$this->app->isAjaxOutput()){

            if($x=$this->app->top_menu->getMenuName('Support/Customers',true)) $x->destroy();
            if($x=$this->app->top_menu->getMenuName('Commerce/Customer',true)) $x->destroy();

            $m = $this->app->top_menu->addMenu('CAF');
                $m->addItem(['Lead Category','icon'=>'fa fa-check-square-o'],'xepan_marketing_marketingcategory');
                $m->addItem(['Lead','icon'=>'fa fa-users'],'xavoc_ispmanager_lead&status=Open');
                $m->addItem(['Installation Due','icon'=>'fa fa-users'],'xavoc_ispmanager_lead_installation');
                $m->addItem(['Installation Assigned','icon'=>'fa fa-users'],'xavoc_ispmanager_lead_installationassigned');
                $m->addItem(['Installed','icon'=>'fa fa-users'],'xavoc_ispmanager_lead_installed');
                $m->addItem(['InDemo','icon'=>'fa fa-users'],'xavoc_ispmanager_user&status=InDemo');
                $m->addItem(['Active User','icon'=>'fa fa-users'],'xavoc_ispmanager_user');
                $m->addItem(['All Lead','icon'=>'fa fa-users'],'xavoc_ispmanager_lead_all');
                $m->addItem(['Convert Customer to ISP User','icon'=>'fa fa-users'],'xavoc_ispmanager_convertcustomertoispuser');
                
            $m = $this->app->top_menu->addMenu('ISP MANAGER');
            // $m->addItem(['Leads','icon'=>'fa fa-users'],'xavoc_ispmanager_lead');
            $m->addItem(['Users','icon'=>'fa fa-check-square-o'],'xavoc_ispmanager_user&status=Active');
            $m->addItem(['Plans','icon'=>'fa fa-check-square-o'],'xavoc_ispmanager_plan&status=Published');
            $m->addItem(['Topups','icon'=>'fa fa-check-square-o'],'xavoc_ispmanager_topup');
            $m->addItem(['Invoices','icon'=>'fa fa-check-square-o'],'xavoc_ispmanager_invoice');
            $m->addItem(['Up-Coming Invoice','icon'=>'fa fa-check-square-o'],'xavoc_ispmanager_upcominginvoice2');
            $m->addItem(['Microtik Routers','icon'=>'fa fa-check-square-o'],'xavoc_ispmanager_client');
            $m->addItem(['Reports','icon'=>'fa fa-cog'],'xavoc_ispmanager_report');
            $m->addItem(['Configuration','icon'=>'fa fa-cog'],'xavoc_ispmanager_configuration');
            $m->addItem(['Log','icon'=>'fa fa-user'],'xavoc_ispmanager_log');
            $m->addItem(['Device Management','icon'=>'fa fa-user'],'xavoc_ispmanager_device');
            $m->addItem(['Employee Payment Collection','icon'=>'fa fa-users'],'xavoc_ispmanager_employeepaymentcollection');
            $m->addItem(['Up coming Invoice Correction','icon'=>'fa fa-users'],'xavoc_ispmanager_invoicecorrection');
            $m->addItem(['Debug RADIUS','icon'=>'fa fa-users'],'xavoc_ispmanager_debug');
            $m->addItem(['Dates Management','icon'=>'fa fa-users'],'xavoc_ispmanager_datesmanage');

            $m = $this->app->top_menu->addMenu('Channel');
            $m->addItem(['channel Mgnt','icon'=>'fa fa-cog'],'xavoc_ispmanager_channel_channel');
            $m->addItem(['Agent Mgnt','icon'=>'fa fa-cog'],'xavoc_ispmanager_channel_agent');
            $m->addItem(['Plan','icon'=>'fa fa-cog'],'xavoc_ispmanager_channel_plan');
            $m->addItem(['Lead','icon'=>'fa fa-cog'],'xavoc_ispmanager_channel_lead');
            $m->addItem(['ISP User','icon'=>'fa fa-cog'],'xavoc_ispmanager_channel_user');
            $m->addItem(['Commission Mgnt','icon'=>'fa fa-cog'],'xavoc_ispmanager_channel_commission');
            $m->addItem(['Invoice','icon'=>'fa fa-cog'],'xavoc_ispmanager_channel_invoice');
            $m->addItem(['Payment Collection','icon'=>'fa fa-cog'],'xavoc_ispmanager_channel_paymentcollection');
            // $m->addItem(['Ticket','icon'=>'fa fa-cog'],'xavoc_ispmanager_channel_ticket');
        }

        $this->addAppFunctions();

        $user = $this->add('xavoc\ispmanager\Model_User');

        // $this->app->addHook('beforeQSPSave',[$user,'updateQSPBeforeSave']);
        $this->app->addHook('invoice_approved',[$user,'invoiceApproved']);
        // $this->app->addHook('invoice_paid',[$user,'invoicePaid']);
        $this->app->addHook('beforeQspDocumentGenerate',[$user,'beforeQspDocumentGenerate']);

        $this->app->addHook('new_lead_added',function($app,$model){
            
            if($model['status'] == "Active" AND $model['assign_to_id'] > 0){
                $lead = $this->add('xavoc\ispmanager\Model_Lead')->load($model['id']);
                $lead->assign($model['assign_to_id']);
            }
        });

        $this->app->addHook('entity_collection',[$this,'exportEntities']);

        // status icon
        $this->app->status_icon["xavoc\ispmanager\Model_BasicPlan"] = ['All'=>' fa fa-globe','Published'=>"fa fa-file-text-o text-success",'UnPublished'=>'fa fa-file-o text-success'];
        $this->app->status_icon["xavoc\ispmanager\Model_UserData"] = ['All'=>' fa fa-globe','Active'=>"fa fa-file-text-o text-success",'InActive'=>'fa fa-file-o text-red'];
        return $this;
    }

    function exportEntities($app,&$array){
        $array['ispmanager_plan'] = ['caption'=>'Plan','type'=>'DropDown','model'=>'xavoc\ispmanager\Model_Plan'];
        $array['ispmanager_user'] = ['caption'=>'ISP User','type'=>'DropDown','model'=>'xavoc\ispmanager\Model_User'];
        $array['ispmanager_Lead'] = ['caption'=>'ISP User','type'=>'DropDown','model'=>'xavoc\ispmanager\Model_Lead'];
        
        // channel
        $array['Channel'] = ['caption'=>'Channel','type'=>'DropDown','model'=>'xavoc\ispmanager\Model_Channel'];
        $array['Channel_Lead'] = ['caption'=>'Channel Lead','type'=>'DropDown','model'=>'xavoc\ispmanager\Model_Channel_Lead'];
        $array['Agent'] = ['caption'=>'Agent','type'=>'DropDown','model'=>'xavoc\ispmanager\Model_Channel_Agent'];
    }

    function setup_pre_frontend(){
        $this->routePages('xavoc_ispmanager');
        $this->addLocation(array('template'=>'templates','js'=>'templates/js','css'=>'templates/css'))
        ->setBaseURL('./shared/apps/xavoc/mlm/');

        return $this;
    }

    function addAppFunctions(){
        
        $this->app->addMethod('byte2human',function($app,$bytes, $decimals = 2){
            $size = array('b','Kb','Mb','Gb','Tb','Pb','Eb','Zb','Yb');
            $factor = floor((strlen($bytes) - 1) / 3);
            return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
        });

        $this->app->addMethod('human2byte',function($app,$value){
              $result =  preg_replace_callback('/^\s*(\d*\.?\d+)\s*(?:([kmgtpy]?)b?)?\s*$/i', function ($m) {
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

              return round($result,0);
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
            
            $now = \DateTime::createFromFormat('Y-m-d', $this->app->today);
            $job = new \Cron\Job\ShellJob();
            $job->setSchedule(new \Cron\Schedule\CrontabSchedule('0-10 0 * * *'));
            if(!$job->getSchedule() || $job->getSchedule()->valid($now)){
                echo " Executing Condition Reset<br/>";
                $this->add('xavoc\ispmanager\Controller_ResetUserPlanAndTopup')->run();
            }

        });

        return $this;
    }

    function resetDB(){
        // run radius sql file to create tables
        // run stored_procedures file to create stored procedures


        preg_match(
                    '|([a-z]+)://([^:]*)(:(.*))?@([A-Za-z0-9\.-]*)'.
                    '(/([0-9a-zA-Z_/\.-]*))|',
                    $this->app->getConfig('dsn'),
                    $matches
                );
        
        $username = $matches[2];
        $database = $matches[7];
        $host = $matches[5];
        $password = $matches[4];

        $pre = '/';
        if((isset($this->app->is_install) && $this->app->is_install) || (isset($this->app->is_admin) && $this->app->is_admin)) $pre= '/../';
        shell_exec("mysql -u$username -p$password -h$host $database < ".getcwd().$pre.'shared/apps/xavoc/ispmanager/stored_procedures.sql');
        $this->app->db->dsql()->expr(file_get_contents(getcwd().$pre.'shared/apps/xavoc/ispmanager/radius.sql'))->execute();
        $this->app->db->dsql()->expr(file_get_contents(getcwd().$pre.'shared/apps/xavoc/ispmanager/isp.sql'))->execute();

        $config_path = getcwd().$pre.'websites/'.$this->app->current_website_name.'/config.php';
        $webapppath = getcwd().$pre.'shared/apps/xavoc/ispmanager/webappconfig';
        file_put_contents($config_path, file_get_contents($config_path). file_get_contents($webapppath));

    }

}
