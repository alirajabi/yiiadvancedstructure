<?php
/**
 * Created by PhpStorm.
 * User: Ali Rajabi - www.alirajabi.com
 * Date: 10/29/13
 * Time: 12:13 PM
 */

//define Folder & Structure
define ('WEB_FOLDER', dirname(__FILE__));
define('VENDOR_FOLDER', WEB_FOLDER . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor');
define('YII_FOLDER', VENDOR_FOLDER . DIRECTORY_SEPARATOR . 'yii' . DIRECTORY_SEPARATOR . 'framework');
define('APP_FOLDER', WEB_FOLDER . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app');
define('ADMIN_FOLDER', WEB_FOLDER . DIRECTORY_SEPARATOR . 'management');
define('MODULES_FOLDER', APP_FOLDER . DIRECTORY_SEPARATOR . 'modules');
define('RESOURCES_FOLDER', WEB_FOLDER . DIRECTORY_SEPARATOR . 'resources');
define('RUNTIME_FOLDER', APP_FOLDER . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'runtime');


// Define EMAIL INFORMATION
define('ADMIN_EMAIL', 'rajabi000@gmail.com');
// Set Time Zone
date_default_timezone_set('Asia/Tehran');

//Load Programmer Helper
require_once(VENDOR_FOLDER . DIRECTORY_SEPARATOR . 'rajabi' . DIRECTORY_SEPARATOR . 'helper' . DIRECTORY_SEPARATOR . 'helper.php');
use \rajabi\helper\RFunc as RFunc;
use \rajabi\helper\RFile as RFile;
use \rajabi\helper\RFileCache as RFileCache;
use \rajabi\helper\RException as RException;


//set environment
require_once(APP_FOLDER . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'environment.php');
$environment = new Environment(Environment::DEVELOPMENT);


//set Module Configs
RFileCache::$directory = RUNTIME_FOLDER . DS . 'cache';
if (FALSE === $modulesConfig = RFileCache::get('moduleConfig')) {
    $modules = RFile::getDirectories(MODULES_FOLDER);
    $modulesConfig = $environment->getModuleConfig();
    if ($modules) {
        foreach ($modules as $module) {
            foreach (array('frontEnd', 'backEnd') as $type) {
                if (file_exists($tempConfig = $module . DS . 'config' . DS . $type . '.php') && is_readable($tempConfig)) {
                    $modulesConfig[$type] = array_merge_recursive($modulesConfig[$type], require_once $tempConfig);
                }
            }
        }
    }
    RFileCache::set('moduleConfig', $modulesConfig);
}
$environment->setModuleConfig($modulesConfig);


//Set session path
RFile::makeDir(RESOURCES_FOLDER);
RFile::makeDir(RUNTIME_FOLDER);
RFile::makeDir(RUNTIME_FOLDER . DS . 'session');
RFile::writeFile(RUNTIME_FOLDER . DS . '.htaccess', "deny from all\n");
RFile::makeDir(WEB_FOLDER . DS . 'assets');
RFile::makeDir(ADMIN_FOLDER . DS . 'assets');

if (RException::get())
    RException::trace();

defined('YII_DEBUG') or define('YII_DEBUG', $environment->getDebug());
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', $environment->getTraceLevel());

//Load Yii Base
require_once(YII_FOLDER . DIRECTORY_SEPARATOR . 'YiiBase.php');

//Yii ide helper
class Yii extends YiiBase
{
    /**
     * @static
     * @return CWebApplication
     */
    public static function app()
    {
        return parent::app();
    }
}

//set Alias Structure
Yii::setPathOfAlias('vendor', VENDOR_FOLDER);

//Load Yii Helper
require_once(VENDOR_FOLDER . DIRECTORY_SEPARATOR . 'rajabi' . DIRECTORY_SEPARATOR . 'yiihelper' . DIRECTORY_SEPARATOR . 'global.php');

