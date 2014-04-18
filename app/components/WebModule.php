<?php
/**
 * Created by PhpStorm.
 * User: Ali Rajabi - www.alirajabi.com
 * Date: 10/29/13
 * Time: 2:11 PM
 */
class WebModule extends CWebModule
{
    /*
     * config Yii FrontEnd And BackEnd Modules
     */
    public function init()
    {
        if (isset (app()->endName) && app()->endName) {
            $this->setControllerPath($this->getControllerPath() . DS . app()->endName);
            $this->setViewPath($this->getViewPath() . DS . app()->endName);
        }

        if (!defined('APP_MODULE'))
            define('APP_MODULE', $this->getId());
        parent::init();
    }
}