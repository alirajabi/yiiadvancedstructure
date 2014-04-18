<?php

class MainController extends BackController
{

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model = new Category();
        $model->onChange = array($this, 'clearCache');
        $this->performAjaxValidation($model);
        if (isset($_POST['Category'])) {
            $model->attributes = $_POST['Category'];
            if ($model->createCategory()) {
                $this->redirect(array('index', 'module' => $_GET['module']));
            }
        }
        $this->render('create', array(
            'model' => $model,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id, $module)
    {
        $model = $this->loadModel($id, $module);
        $this->performAjaxValidation($model);
        if (isset($_POST['Category'])) {
            $model->attributes = $_POST['Category'];
            if ($model->save()) {
                $this->redirect(array('index', 'module' => $_GET['module']));

            }
        }
        $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id, $module)
    {
        $model = $this->loadModel($id, $module);
        $model->deleteCategory();
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index', 'module' => $_GET['module']));
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $model = new Category();
        $model->unsetAttributes(); // clear any default values
        $model->init();
        if (isset($_GET['Category']))
            $model->attributes = $_GET['Category'];

        $this->render('index', array(
            'model' => $model,
        ));
    }

    /**
     * Rearrange category orders
     */
    public function actionArrange()
    {
        if (isset($_POST['data']) && $data = CJSON::decode($_POST['data'], FALSE)) {
            $model = new Category();
            $model->onChange = array($this, 'clearCache');
            if ($model->arrangeCategory($data)) {
                user()->setFlash('success', t('app', 'operationSuccess'));
            } else {
                user()->setFlash('error', t('error', 'internalServerError'));
            }
        }
        $this->redirect(array('index', 'module' => $_GET['module']));
    }

    /**
     * Performs the AJAX validation.
     * @param Category $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'category-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }


    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param $id
     * @param int|null $module
     * @return CActiveRecord|Category
     * @throws CHttpException
     */
    public function loadModel($id, $module)
    {
        $model = Category::model()->findByPk(array('id' => $id, 'module' => $module));
        $model->onChange = array($this, 'clearCache');
        if ($model === null)
            throw new CHttpException(404, t('error', 'pageNotExist'));
        return $model;
    }

    /**
     * Delete Category List Cache
     * @param $event
     * @return bool
     */
    protected function clearCache($event)
    {
        app()->cache->delete('categoriesList' . ucfirst($event->params['category']->module) . ucfirst(app()->language));
        app()->cache->delete('categoriesTreeList' . ucfirst($event->params['category']->module) . ucfirst(app()->language));
        return TRUE;
    }


    protected function beforeAction($action)
    {
        if (!isset($_GET['module']) || !$_GET['module'] || !app()->modulesInfo->module($_GET['module'])->exist())
            throw new CHttpException(404, t('error', 'pageNotExist'));
        return parent::beforeAction($action);
    }
}
