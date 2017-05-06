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
        $m->addItem(['Configuration','icon'=>'fa fa-cog'],'xavoc_ispmanager_configuration');
        $m->addItem(['test','icon'=>'fa fa-cog'],'xavoc_ispmanager_test');

        $this->addAppFunctions();

        return $this;
    }

    function addAppFunctions(){
        $this->app->addMethod('toMB',function($app,$data=null){
            return $data;
        });
    }

    function setup_frontend(){
        $this->routePages('xavoc_ispmanager');
        $this->addLocation(array('template'=>'templates','js'=>'templates/js','css'=>'templates/css'))
        ->setBaseURL('./shared/apps/xavoc/ispmanager/');
        return $this;
    }

}
