<?php
//set Defines
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'startup.php');

Yii::createWebApplication($environment->getConfig('backEnd'))->runEnd('backEnd');
