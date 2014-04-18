<?php
/* @var $this MainController */
/* @var $model Category */
/* @var $form CActiveForm */
?>

<div class="wide form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    )); ?>

    <div class="row">
        <?php echo $form->label($model, 'id'); ?>
        <?php echo $form->textField($model, 'id'); ?>
    </div>


    <div class="row">
        <?php echo $form->label($model, 'title'); ?>
        <?php echo $form->textField($model, 'title', array('size' => 16, 'maxlength' => 32)); ?>
    </div>


    <div class="row">
        <?php echo $form->label($model, 'active'); ?>
        <?php echo $form->textField($model, 'active'); ?>
    </div>

    <div class="row">
        <?php echo $form->label($model, 'cDate'); ?>
        <?php echo $form->textField($model, 'cDate'); ?>
    </div>


    <div class="row">
        <?= CHtml::hiddenField('module', $_GET['module']) ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton(t('app', 'search')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- search-form -->