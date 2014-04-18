<?php

/**
 * Created by PhpStorm.
 * User: Ali Rajabi - www.alirajabi.com
 * Date: 11/19/13
 * Time: 10:16 AM
 */
class FrontController extends Controller
{

    public $layout = '//layouts/column2';


    public function init()
    {
        if (!defined('PUBLIC_FOLDER'))
            define('PUBLIC_FOLDER', baseUrl() . DS . 'public');
        parent::init();
    }
}