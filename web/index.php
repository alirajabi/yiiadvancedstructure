<?php
//set Defines & Yii Helper
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'startup.php');

Yii::createWebApplication($environment->getConfig('frontEnd'))->runEnd('frontEnd');
