<?php
/**
 * Created by PhpStorm.
 * User: Ali Rajabi - www.alirajabi.com
 * Date: 10/28/13
 * Time: 12:56 PM
 */
class Environment
{

    const DEVELOPMENT = 100;
    const STAGE = 300;
    const PRODUCTION = 400;

    private $_mode = 0;
    private $_debug;
    private $_trace_level;
    private $_config;
    private $_moduleConfig = array('backEnd' => array(), 'frontEnd' => array());

    /**
     * Main configuration
     * This is the general configuration that uses all environments
     */
    private function _main()
    {
        return array(
            'basePath' => APP_FOLDER,
            'runtimePath' => RUNTIME_FOLDER,
            'name' => 'Yii1.1-Base',
            //Set default controller
            'defaultController' => 'default',
            // preloading 'log' & 'input(XssFilter)' component
            'preload' => array('log', 'input'),

            // autoloading model and component classes
            'import' => array(
                'application.models.*',
                'application.components.*',

            ),
            'modules' => array(),

            // application components
            'components' => array(
                'user' => array(
                    // enable cookie-based authentication
                    'allowAutoLogin' => true,
                ),
                // uncomment the following to enable URLs in path-format

                'urlManager' => array(
                    'urlFormat' => 'path',
                    'rules' => array(
                        '<controller:\w+>/<id:\d+>' => '<controller>/view',
                        '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                        '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                    ),
                ),

                /*'db'=>array(
                    'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
                ),*/
                // uncomment the following to use a MySQL database
                /*
                                'db' => array(
                                    'connectionString' => 'mysql:host=localhost;dbname=profile',
                                    'emulatePrepare' => true,
                                    'username' => 'root',
                                    'password' => 'websepanta',
                                    'charset' => 'utf8',
                                    'schemaCachingDuration' => 2,
                        'enableProfiling'=>true,
        'enableParamLogging'=>true,
                                ),
                */
                'errorHandler' => array(
                    // use 'default/error' action to display errors
                    'errorAction' => 'default/error',
                ),
                //Set Xss Filter
                'input' => array(
                    'class' => 'CmsInput',
                    'cleanPost' => true,
                    'cleanGet' => true,
                ),
                'modulesInfo' => array(
                    'class' => 'application.components.ModulesInfo',
                ),
            ),
            // application-level parameters that can be accessed
            // using Yii::app()->params['paramName']
            'params' => array(
                // this is used in contact page
                'adminEmail' => ADMIN_EMAIL,
                // Application-level parameters
                'environment' => $this->_mode,
                'languages' => array(
                    'fa' => array(
                        'interface' => 'پارسی'
                    ),
                    'en' => array(
                        'interface' => 'English'
                    )
                )
            ),
            'behaviors' => array(
                'runEnd' => array(
                    'class' => 'application.components.WebApplicationEndBehavior',
                ),
            ),
        );
    }

    /**
     * Stage configuration
     * This is the general configuration that uses Stage
     */
    private function _stage()
    {
        return array(
            'components' => array(
                'log' => array(
                    'class' => 'CLogRouter',
                    'routes' => array(
                        array(
                            'class' => 'CFileLogRoute',
                            'levels' => 'error, warning, trace, info',
                        ),

                    ),
                ),
            ),
        );
    }

    /**
     * Development configuration
     * This is the general configuration that uses development
     */
    private function _development()
    {
        return array(
            // autoloading model and component classes
            'import' => array(
                'application.extensions.yiidebugtb.*',

            ),
            'modules' => array(
                // uncomment the following to enable the Gii tool
                'gii' => array(
                    'class' => 'system.gii.GiiModule',
                    'password' => false,
                    // If removed, Gii defaults to localhost only. Edit carefully to taste.
                    'ipFilters' => array('127.0.0.1', '::1'),
                ),

            ),
            'components' => array(
                'log' => array(
                    'class' => 'CLogRouter',
                    'routes' => array(
                        array(
                            'class' => 'CFileLogRoute',
                            'levels' => 'error, warning',
                            'enabled' => true
                        ),
                        // uncomment the following to show log messages on web pages
                        array(
                            'class' => 'CWebLogRoute',
                            'enabled' => false
                        ),
                        array( // configuration for the toolbar
                            'class' => 'XWebDebugRouter',
                            'config' => 'alignLeft, opaque, runInDebug, fixedPos, collapsed, yamlStyle',
                            'levels' => 'error, warning, trace, profile, info',
                            'allowedIPs' => array('127.0.0.1', '::1', '192.168.1.54', '192\.168\.1[0-5]\.[0-9]{3}'),
                        ),
                    ),
                )
            )
        );
    }

    /*
     * get backEnd / frontEnd Config Files
     */
    private function _getConfig($section)
    {
        return require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . $section . '.php');
    }


    /**
     * Production configuration
     * This is the general configuration that uses production
     */
    private function _production()
    {
        return array(
            'components' => array(
                'log' => array(
                    'class' => 'CLogRouter',
                    'routes' => array(
                        array(
                            'class' => 'CFileLogRoute',
                            'levels' => 'error, warning',
                            'enabled' => true
                        ),
                        // Send errors via email to the system admin
                        array(
                            'class' => 'CEmailLogRoute',
                            'levels' => 'error, warning',
                            'emails' => ADMIN_EMAIL,
                        ),
                    ),
                )
            )
        );
    }


    /**
     * Returns the debug mode
     * @return Bool
     */
    public function getDebug()
    {
        return $this->_debug;
    }

    /**
     * Returns the trace level for YII_TRACE_LEVEL
     * @return int
     */
    public function getTraceLevel()
    {
        return $this->_trace_level;
    }

    /**
     * Returns the configuration array depending on the mode
     * you choose
     * @return array
     */
    public function getConfig($section)
    {
        if ($this->getModuleConfig($section))
            return array_merge_recursive($this->getModuleConfig($section), $this->_getConfig($section), $this->_config);
        else
            return
                array_merge_recursive($this->_getConfig($section), $this->_config);
    }

    /**
     * @param $config
     *
     */
    public function setModuleConfig($config)
    {
        $this->_moduleConfig = $config;
    }

    /**
     * @param $section
     * @return null
     *
     */
    public function getModuleConfig($section = NULL)
    {
        if (!$section)
            return $this->_moduleConfig;
        return $this->_moduleConfig[$section];
    }

    /**
     * Initilizes the Environment class with the given mode
     * @param constant $mode
     */
    function __construct($mode)
    {
        $this->_mode = $mode;
        $this->setConfig();
    }

    /**
     * Sets the configuration for the choosen environment
     *
     */
    private function setConfig()
    {
        switch ($this->_mode) {
            case self::DEVELOPMENT:
                error_reporting(1);
                $this->_config = array_merge_recursive($this->_main(), $this->_development());
                $this->_debug = TRUE;
                $this->_trace_level = 3;
                break;

            case self::STAGE:
                error_reporting(1);
                $this->_config = array_merge_recursive($this->_main(), $this->_stage());
                $this->_debug = TRUE;
                $this->_trace_level = 3;
                break;
            case self::PRODUCTION:
                error_reporting(0);
                $this->_config = array_merge_recursive($this->_main(), $this->_production());
                $this->_debug = FALSE;
                $this->_trace_level = 0;
                break;
            default:
                error_reporting(0);
                $this->_config = $this->_main();
                $this->_debug = TRUE;
                $this->_trace_level = 0;
                break;
        }
    }
}

