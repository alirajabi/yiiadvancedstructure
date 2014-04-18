<?php
/* @var $this MainController */
/* @var $model Category */
$this->pageTitle = Yii::app()->name . ' - ' . t('app', 'opMainUpdate');

$this->breadcrumbs = array(
    t('app', 'opMainIndex') => array('index', 'module' => $_GET['module']),
    $model->title => array('view', 'id' => $model->id),
    t('app', 'opMainUpdate'),
);

$this->menu = array(
    array('label' => t('app', 'opMainIndex'), 'url' => array('index', 'module' => $_GET['module'])),
    array('label' => t('app', 'opMainCreate'), 'url' => array('create', 'module' => $_GET['module'])),

);
?>

    <h1><?= t('app', 'opMainUpdate') . ' ' . $model->title; ?></h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>