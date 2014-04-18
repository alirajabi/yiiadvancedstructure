<?php
/* @var $this MainController */
/* @var $model Category */
/* @var $form CActiveForm */
?>

<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'category-form',
        'enableAjaxValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true
        )
    )); ?>

    <p class="note"><?= t('app', 'fieldsRequired') ?></p>

    <?php echo $form->errorSummary($model); ?>
    <?php if ($model->getIsNewRecord()): ?>
        <div class="row">
            <?php echo $form->labelEx($model, 'parent'); ?>
            <?php echo $form->dropDownList($model, 'parent', CHtml::listData(Category::getCategoryTreeList($_GET['module'], app()->language), 'id', 'title'), array('empty' => '')); ?>
            <?php echo $form->error($model, 'parent'); ?>
        </div>
    <?php endif; ?>
    <div class="row">
        <?php echo $form->labelEx($model, 'title'); ?>
        <?php echo $form->textField($model, 'title', array('size' => 32, 'maxlength' => 32)); ?>
        <?php echo $form->error($model, 'title'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'statement'); ?>
        <?php echo $form->textField($model, 'statement', array('size' => 60, 'maxlength' => 255)); ?>
        <?php echo $form->error($model, 'statement'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'description'); ?>
        <?php echo $form->textArea($model, 'description', array('rows' => 6, 'cols' => 50)); ?>
        <?php echo $form->error($model, 'description'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model, 'status'); ?>
        <?php echo $form->dropDownList($model, 'active', array('1' => t('app', 'active'), '0' => t('app', 'inActive')), array('separator' => null)); ?>
        <?php echo $form->error($model, 'active'); ?>
    </div>
    <div class="row">
        <?php $this->widget('application.widgets.MetaWidget', array('refModule' => APP_MODULE, 'refModel' => $model)) ?>
    </div>
    <div class="row">

        <?php $this->widget('application.widgets.AttachmentJUIWidget', array(
            'refModule' => APP_MODULE,
            'refModel' => $model,
            'refParams' => array(
                array('name' => 'defaultImage', 'limit' => 1),
            )
        ))?>
    </div>
    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? t('app', 'create') : t('app', 'save')); ?>
    </div>

    <?php $this->endWidget(); ?>
</div><!-- form -->