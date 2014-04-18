<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
      xml:lang="<?php echo $tmpLang = Yii::app()->language == 'fa' ? 'fa' : 'en'; ?>'"
      lang="<?= $tmpLang ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="language" content="<?= $tmpLang ?>"/>
    <link rel="stylesheet" type="text/css" href="<?= baseUrl() ?>/public/client/css/screen.css"
          media="screen, projection"/>
    <link rel="stylesheet" type="text/css" href="<?= baseUrl() ?>/public/client/css/print.css"
          media="print"/>
    <!--[if lt IE 8]>
    <link rel="stylesheet" type="text/css" href="<?= baseUrl() ?>/public/client/css/ie.css"
          media="screen, projection"/>
    <![endif]-->

    <link rel="stylesheet" type="text/css" href="<?= baseUrl() ?>/public/client/css/main.css"/>
    <link rel="stylesheet" type="text/css" href="<?= baseUrl() ?>/public/client/css/form.css"/>

    <?php
    clientScript()->registerCoreScript('jquery');
    clientScript()->registerScriptFile(app()->assetManager->publish(PUBLIC_FOLDER . DS . 'admin' . DS . 'js' . DS . 'public.js'), CClientScript::POS_HEAD);

    ?>
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
<body>

<div class="container" id="page">

    <div id="header">
        <div id="logo"><?php echo CHtml::encode(Yii::app()->name); ?></div>
    </div>
    <!-- header -->
    <div id="mainmenu">
        <?php $this->widget('zii.widgets.CMenu', array(
            'items' => array(
                array('label' => 'Home', 'url' => array('/default/index')),
                array('label' => 'About', 'url' => array('/default/page', 'view' => 'about')),
                array('label' => 'Contact', 'url' => array('/default/contact')),
                array('label' => 'Login', 'url' => array('/default/login'), 'visible' => Yii::app()->user->isGuest),
                array('label' => 'Logout (' . Yii::app()->user->name . ')', 'url' => array('/default/logout'), 'visible' => !Yii::app()->user->isGuest)
            ),
        )); ?>
    </div>
    <!-- mainmenu -->
    <?php if (isset($this->breadcrumbs)): ?>
        <?php $this->widget('zii.widgets.CBreadcrumbs', array(
            'links' => $this->breadcrumbs,
        )); ?><!-- breadcrumbs -->
    <?php endif ?>

    <?php echo $content; ?>

    <div class="clear"></div>
    <div id="footer">
        Copyright &copy; <?php echo date('Y'); ?> by YiiFramework.<br/>

        <div class="clear"></div>
    </div>
    <!-- footer -->
</div>
<!-- page -->
</body>
</html>
