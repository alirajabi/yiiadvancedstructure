<?php
/* @var $this MainController */
/* @var $model Category */
$this->pageTitle = Yii::app()->name . ' - ' . t('app', 'opMainCreate');


$this->breadcrumbs = array(
    t('app', 'opMainIndex') => array('index', 'module' => $_GET['module']),
    t('app', 'opMainCreate'),
);

$this->menu = array(
    array('label' => t('app', 'opMainIndex'), 'url' => array('index', 'module' => $_GET['module'])),
);
?>

    <h1><?= t('app', 'opMainCreate') ?></h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>