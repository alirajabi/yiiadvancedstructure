<?php
/* @var $this MainController */
/* @var $model Category */
$this->pageTitle = Yii::app()->name . ' - ' . t('app', 'opMainIndex');

$this->breadcrumbs = array(
    t('app', 'opMainIndex'),
);

$this->menu = array(
    array('label' => t('app', 'opMainCreate'), 'url' => array('create', 'module' => $_GET['module'])),
);

clientScript()->registerScriptFile(app()->assetManager->publish(PUBLIC_FOLDER . DS . 'global' . DS . 'js' . DS . 'jquery.mjs.nestedSortable.js'), CClientScript::POS_BEGIN);
clientScript()->registerScript('sort', "
        $(document).ready(function () {
            $('ol.sortable').nestedSortable({
                forcePlaceholderSize: true,
                handle: 'div',
                helper: 'clone',
                items: 'li',
                opacity: .6,
                placeholder: 'placeholder',
                revert: 250,
                tabSize: 25,
                tolerance: 'pointer',
                toleranceElement: '> div',
                maxLevels: 10,
                disableNesting: 'no-nest',
                isTree: true,
                expandOnHover: 700,
                startCollapsed: true,
                rtl: true
            });


        });
    var arrangeCategory = function (obj) {
    obj.submit(function (e) {
        var data = obj.find('input[name=data]');
        var orderCategories = $('ol.sortable').nestedSortable('toArray', {
            startDepthCount: 0
        });
        data.val(JSON.stringify(orderCategories));
        if (data.val() == '')
            $(document).reload();
        else
            obj.submit();
    });

};", CClientScript::POS_HEAD);

?>
    <h1><?= t('app', 'opMainIndex') ?></h1>
<?php



$categories = $model->search();
$depth = -1;?>
    <div id="sortableList">
        <?php
        foreach ($categories as $key => $category):
        if ($depth < $category->level) : ?>
        <ol <?= !$key ? 'class="sortable"' : '' ?>>
            <?php
            elseif ($depth > $category->level) :
                echo str_repeat("</ol>", $depth - $category->level);
            endif;
            ?>
            <li id="listItems_<?= $category->id ?>">
                <div><?= $category->title ?>
                    <span class="sortableList button">
                                      <?= user()->checkAccess('categoryMainUpdate') ? CHtml::link(t('app', 'update'), app()->createUrl(APP_MODULE . '/' . APP_CONTROLLER . '/update', array('module' => $_GET['module'], 'id' => $category->id))) : '' ?>
                                      <?= user()->checkAccess('categoryMainDelete') ? CHtml::link(t('app', 'delete'), app()->createUrl(APP_MODULE . '/' . APP_CONTROLLER . '/delete', array('module' => $_GET['module'], 'id' => $category->id)), array('confirm' => t('app', 'deleteConfirm'))) : '' ?>
                        </span>
                </div>
                <?php
                $depth = $category->level;
                endforeach;
                echo str_repeat("</ol>", $depth + 1);
                ?>
                <?php if (user()->checkAccess('categoryMainArrange')): ?>
                    <div style="margin:4px">
                        <?= CHtml::form(app()->createUrl('category/main/arrange', array('module' => $_GET['module'])), 'post', array('id' => 'categoryIndexFrom')) ?>

                        <div>
                            <?= CHtml::hiddenField('data', '') ?>
                        </div>

                        <div clas="row button">
                            <?php echo CHtml::submitButton(t('app', 'save'), array('onClick' => 'arrangeCategory($("#categoryIndexFrom"))')); ?>
                        </div>
                        <?= CHtml::endForm() ?>
                    </div>
                <?php endif; ?>
    </div>
<?php /*$this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'category-grid',
        'dataProvider' => $model->search(),
        'filter' => $model,
        'columns' => array(
            'id',
            'module',
            'title',
            'statement',
            'description',
            'level',

            'category_right',
            'category_left',
            'active',
            'cDate',
            'lang',

            array(
                'class' => 'CButtonColumn',
                'template' => '{update}{delete}',
                'buttons' => array(
                    'update' => array('url' => 'app()->createUrl(APP_MODULE.DS.APP_CONTROLLER.DS."update",array("id"=>$data->id,"module"=>$_GET["module"]))'),
                    'delete' => array('url' => 'app()->createUrl(APP_MODULE.DS.APP_CONTROLLER.DS."delete",array("id"=>$data->id,"module"=>$_GET["module"]))'),
                )
            ),
        )
    )
);*/
?>