<?php
/**
 * Created by PhpStorm.
 * User: Ali Rajabi - www.alirajabi.com
 * Date: 10/28/13
 * Time: 2:44 PM
 */


/**
 * This is the shortcut to Yii::app()
 */
function app()
{
    return Yii::app();
}

/**
 * @return mixed
 * This is the shortcut to Yii::app()->db
 */
function db()
{
    return Yii::app()->db;
}

/**
 * This is the shortcut to Yii::app()->clientScript
 */
function clientScript()
{
    // You could also call the client script instance via Yii::app()->clientScript
    // But this is faster
    return Yii::app()->getClientScript();
}

/**
 * This is the shortcut to Yii::app()->user.
 */
function user()
{
    return Yii::app()->getUser();
}

/**
 * This is the shortcut to Yii::app()->createUrl()
 */
function url($route, $params = array(), $ampersand = '&')
{
    return Yii::app()->createUrl($route, $params, $ampersand);
}


/**
 * Set the key, value in Session
 * @param object $key
 * @param object $value
 * @return boolean
 */
function setSession($key, $value)
{
    return Yii::app()->getSession()->add($key, $value);
}

/**
 * Get the value from key in Session
 * @param object $key
 *
 * @return object
 */
function getSession($key)
{
    return Yii::app()->getSession()->get($key);
}

/**
 * Remove the value from key in Session
 * @param object $key
 *
 * @return object
 */
function removeSession($key)
{
    return Yii::app()->getSession()->remove($key);
}


/**
 * This is the shortcut to Yii::t() with default category = 'stay'
 */
function t($category = 'cms', $message, $params = array(), $source = null, $language = null)
{
    return Yii::t($category, $message, $params, $source, $language);
}


/**
 * This is the shortcut to Yii::app()->request->baseUrl
 * If the parameter is given, it will be returned and prefixed with the app baseUrl.
 */
function baseUrl($url = null)
{
    static $baseUrl;
    if ($baseUrl === null)
        $baseUrl = Yii::app()->getRequest()->getBaseUrl();
    return $url === null ? $baseUrl : $baseUrl . '/' . ltrim($url, '/');
}


/**
 * Returns the named application parameter.
 * This is the shortcut to Yii::app()->params[$name].
 */
function param($name)
{
    return Yii::app()->params[$name];
}

/**
 * shortcut Yii::app()->cache
 * @return mixed
 */
function cache()
{
    return Yii::app()->cache;
}