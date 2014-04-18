<?php
/**
 * Created by PhpStorm.
 * User: Ali Rajabi - www.alirajabi.com
 * Date: 11/4/13
 * Time: 3:36 PM
 */

/**
 * This is the model class for table "category".
 *
 * The followings are the available columns in table 'category':
 * @property integer $id
 * @property string $module
 * @property string $title
 * @property string $statement
 * @property string $description
 * @property string $defaultImage
 * @property integer $level
 * @property integer $category_left
 * @property integer $category_right
 * @property integer $active
 * @property integer $cDate
 * @property string $lang
 */
class Category extends CActiveRecord
{
    //variable
    public $parent;


    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'category';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
// NOTE: you should only define rules for those attributes that
// will receive user inputs.
        return array(
            array('module, title, level, category_left, category_right, active, cDate, lang', 'required'),
            array('parent,level, category_left, category_right, active, cDate', 'numerical', 'integerOnly' => true),
            array('module', 'length', 'max' => 12),
            array('title', 'length', 'max' => 32),
            array('statement', 'length', 'max' => 255),
            array('defaultImage', 'length', 'max' => 32),
            array('lang', 'length', 'max' => 4),
            array('description', 'safe'),
// The following rule is used by search().
// @todo Please remove those attributes that should not be searched.
            array('id, module, title, statement, description, defaultImage, level, category_left, category_right, active, cDate, lang', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return eval(app()->modulesInfo->module('category')->relations()->getById('category'));
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => t('app', 'id'),
            'module' => t('app', 'module'),
            'title' => t('app', 'title'),
            'statement' => t('app', 'statement'),
            'description' => t('app', 'description'),
            'defaultImage' => t('app', 'defaultImage'),
            'level' => t('app', 'level'),
            'category_left' => t('app', 'category_left'),
            'category_right' => t('app', 'category_right'),
            'active' => t('app', 'active'),
            'cDate' => t('app', 'cDate'),
            'lang' => t('app', 'lang'),
            'status' => t('app', 'status'),
            'parent' => t('app', 'parent'),
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        $params = array();
        $criteria = new CDbCriteria;
        $criteria->select = 'node.title AS `title`,node.id AS `id`,node.level AS `level`';
        $criteria->alias = 'node';
        $criteria->order = 'node.category_left';
        $criteria->join = 'INNER JOIN category parent ON  node.category_left BETWEEN parent.category_left AND parent.category_right AND parent.module=:parentModule AND parent.lang=:parentLang';
        $criteria->group = 'node.id';
        $params = array_merge($params, array(':parentModule' => $this->module));
        $params = array_merge($params, array(':parentLang' => $this->lang));

        $criteria->addCondition(array('node.module=:module'));
        $params = array_merge($params, array(':module' => $this->module));
        $criteria->addCondition(array('node.lang=:lang'));
        $params = array_merge($params, array(':lang' => $this->lang));
        $criteria->params = $params;
        $data = self::model()->findAll($criteria);
        return $data;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Category the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public function createCategory()
    {

        $this->cDate = time();

        $lastLeft = app()->db->createCommand()
            ->select('category_left AS lastLeft,level ')
            ->from('category')
            ->where('module=:module  AND lang=:lang AND id=:id', array(':module' => $this->module, ':lang' => $this->lang, ':id' => $this->parent ? $this->parent : 0))
            ->queryRow();
        $this->level = $lastLeft ? (int)$lastLeft['level'] + 1 : 0;

        $lastLeft['lastLeft'] = (int)$lastLeft['lastLeft'] ? (int)$lastLeft['lastLeft'] : 0;

        $this->category_right = $lastLeft['lastLeft'] + 2;
        $this->category_left = $lastLeft['lastLeft'] + 1;

        $transaction = app()->db->beginTransaction();
        try {
            $command = app()->db->createCommand('UPDATE `category` SET category_right=category_right+2 WHERE category_right > :lastLeft AND module=:module AND lang=:lang');
            $command->bindParam(":lastLeft", $lastLeft['lastLeft'], PDO::PARAM_STR);
            $command->bindParam(":module", $this->module, PDO::PARAM_STR);
            $command->bindParam(":lang", $this->lang, PDO::PARAM_STR);
            $command->execute();

            $command = app()->db->createCommand('UPDATE `category` SET category_left=category_left+2 WHERE category_left > :lastLeft AND module=:module AND lang=:lang');
            $command->bindParam(":lastLeft", $lastLeft['lastLeft'], PDO::PARAM_STR);
            $command->bindParam(":module", $this->module, PDO::PARAM_STR);
            $command->bindParam(":lang", $this->lang, PDO::PARAM_STR);
            $command->execute();

        } catch (CDbException $e) {
            $transaction->rollback();
            return FALSE;

        }

        if (!$this->save()) {
            $transaction->rollback();
            return FALSE;
        } else {
            $transaction->commit();
            $this->onChange(new CEvent($this, array('category' => $this)));
            return TRUE;
        }

    }

    public function deleteCategory()
    {
        $width = $width = ((int)$this->category_right - (int)$this->category_left) + 1;
        $transaction = app()->db->beginTransaction();
        try {
            $command = app()->db->createCommand('DELETE FROM `category`  WHERE category_left BETWEEN :left AND :right AND module=:module AND lang=:lang');
            $command->bindParam(":left", $this->category_left, PDO::PARAM_STR);
            $command->bindParam(":right", $this->category_right, PDO::PARAM_STR);
            $command->bindParam(":module", $this->module, PDO::PARAM_STR);
            $command->bindParam(":lang", $this->lang, PDO::PARAM_STR);
            $command->execute();
        } catch (CDbException $e) {
            $transaction->rollback();
            return FALSE;
        }

        //just for raise delete event
        try {
            $this->delete();
        } catch (CDbException $e) {
            $transaction->rollback();
            return FALSE;
        }

        try {
            $command = app()->db->createCommand('UPDATE `category` SET category_right=category_right-:width WHERE category_right > :right AND module=:module AND lang=:lang');
            $command->bindParam(":width", $width, PDO::PARAM_STR);
            $command->bindParam(":right", $this->category_right, PDO::PARAM_STR);
            $command->bindParam(":module", $this->module, PDO::PARAM_STR);
            $command->bindParam(":lang", $this->lang, PDO::PARAM_STR);
            $command->execute();

            $command = app()->db->createCommand('UPDATE `category` SET category_left=category_left-:width WHERE category_left > :right AND module=:module AND lang=:lang');
            $command->bindParam(":width", $width, PDO::PARAM_STR);
            $command->bindParam(":right", $this->category_right, PDO::PARAM_STR);
            $command->bindParam(":module", $this->module, PDO::PARAM_STR);
            $command->bindParam(":lang", $this->lang, PDO::PARAM_STR);
            $command->execute();

        } catch (CDbException $e) {
            $transaction->rollback();
            return FALSE;
        }


        $transaction->commit();
        $this->onChange(new CEvent($this, array('category' => $this)));
        return TRUE;


    }

    public function arrangeCategory($tempCategories)
    {
        $transaction = app()->db->beginTransaction();
        try {
            //make WHEN THEN statement
            foreach ($tempCategories as $tempCategory) {
                if (!$tempCategory->item_id)
                    continue;
                $left[] = 'WHEN \'' . $tempCategory->item_id . '\' THEN \'' . $tempCategory->left . '\'';
                $right[] = 'WHEN \'' . $tempCategory->item_id . '\' THEN \'' . $tempCategory->right . '\'';
                $depth[] = 'WHEN \'' . $tempCategory->item_id . '\' THEN \'' . $tempCategory->depth . '\'';

            }

            $command = app()->db->createCommand('UPDATE `category` SET category_left=CASE `id` ' . join(' ', $left) . ' ELSE category_left END ,category_right=CASE `id` ' . join(' ', $right) . ' ELSE category_right END WHERE module=:module AND lang=:lang');
            $command->bindParam(":module", $this->module, PDO::PARAM_STR);
            $command->bindParam(":lang", $this->lang, PDO::PARAM_STR);
            $command->execute();

        } catch (CDbException $e) {
            $transaction->rollback();
            return FALSE;
        }

        try {
            $command = app()->db->createCommand('UPDATE `category` SET level=CASE `id` ' . join(' ', $depth) . ' ELSE level END WHERE module=:module AND lang=:lang');
            $command->bindParam(":module", $this->module, PDO::PARAM_STR);
            $command->bindParam(":lang", $this->lang, PDO::PARAM_STR);
            $command->execute();

        } catch (CDbException $e) {
            $transaction->rollback();
            return FALSE;
        }
        $this->onChange(new CEvent($this, array('category' => $this)));
        $transaction->commit();
        return TRUE;
    }

    /**
     * Get all categories
     * @param $module
     * @param $lang
     * @return CActiveRecord[]
     */
    public static function getCategoryList($module, $lang)
    {
        if (FALSE == $categoriesList = app()->cache->get('categoriesList' . ucfirst($module) . ucfirst($lang))) {
            $categoriesList = self::model()->findAll('module=:module AND lang=:lang', array(':module' => $module, ':lang' => $lang));
            if ($categoriesList)
                app()->cache->set('categoriesList' . ucfirst($module) . ucfirst($lang), $categoriesList);
        }
        return $categoriesList;
    }

    /**
     * Get all category with depth in tree view
     * @param $module
     * @param $lang
     * @return CActiveRecord[]
     */
    public static function getCategoryTreeList($module, $lang)
    {
        if (FALSE == $categoriesList = app()->cache->get('categoriesTreeList' . ucfirst($module) . ucfirst($lang))) {
            $categoriesList = app()->db->createCommand()->select("node.id, CONCAT(REPEAT('-',node.level-1),node.title) AS title")
                ->from('category node')
                ->join('category parent', 'node.category_left between parent.category_left AND parent.category_right')
                ->where('node.module=:module AND node.lang=:lang', array(':module' => $module, ':lang' => $lang))
                ->order('node.category_left')
                ->group('node.title')
                ->queryAll();
            if ($categoriesList)
                app()->cache->set('categoriesTreeList' . ucfirst($module) . ucfirst($lang), $categoriesList);
        }
        return $categoriesList;
    }


    /**
     * change category event
     * @param $event
     */
    public function onChange($event)
    {
        $this->raiseEvent('onChange', $event);
    }

    /**
     * @return array|mixed|void
     */
    public function primaryKey()
    {
        return array('id', 'module');
    }

    public function init()
    {
        if (isset($_GET['module']) && app()->modulesInfo->module($_GET['module'])->exist())
            $this->module = $_GET['module'];
        $this->lang = app()->language;
        parent::init();
    }

    /**
     * events
     */
    protected function afterSave()
    {
        $this->onChange(new CEvent($this, array('category' => $this)));
        return parent::afterSave();
    }

}