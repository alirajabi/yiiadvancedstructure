<?php
/**
 * Created by PhpStorm.
 * User: Ali Rajabi - www.alirajabi.com
 * Date: 11/10/13
 * Time: 11:43 AM
 */
use \rajabi\helper\RFile as RFile;
use \rajabi\helper\RException as RException;


class ModulesInfo extends CComponent
{
    private $_modulesInfo;
    private $_info = NULL;


    /**
     * Get modules information
     */
    public function init()
    {
        if (FALSE == $this->_modulesInfo = app()->cache->get('modulesInfo')) {
            $modules = RFile::getDirectories(MODULES_FOLDER);
            if ($modules) {
                foreach ($modules as $module) {
                    if (file_exists($tempConfig = $module . DS . 'config' . DS . 'info.php') && is_readable($tempConfig)) {
                        $tempConfig = require_once $tempConfig;
                        $this->_modulesInfo[$tempConfig['name']] = $tempConfig;
                    }
                }
            }
            //set Base Info
            if (file_exists($tempConfig = APP_FOLDER . DS . 'config' . DS . 'info.php') && is_readable($tempConfig)) {
                $tempConfig = require_once $tempConfig;
                $this->_modulesInfo[$tempConfig['name']] = $tempConfig;
            }
            if (RException::get()) {
                RException::trace();

            } else {
                app()->cache->set('modulesInfo', $this->_modulesInfo);
            }
        }

    }

    /**
     * Get all modules
     * @return null
     */
    public function getModules()
    {
        return $this->_modulesInfo ? $this->_modulesInfo : NULL;
    }

    /**
     * If isset module return module info
     * @param $returnModule
     */
    public function module($returnModule)
    {
        $this->_info = isset($this->_modulesInfo[$returnModule]) ? $this->_modulesInfo[$returnModule] : NULL;
        return $this;
    }


    /**
     * Get all module's information
     * @return info|null
     */
    public function getAll()
    {
        return $this->_info;

    }

    /**
     * Get a module's information
     * @param $id
     * @return info|null
     */
    public function getById($id)
    {
        return isset($this->_info[$id]) ? $this->_info[$id] : NULL;
    }


    /**
     * check Module is exist
     * @param $returnModule
     * @return bool
     */
    public function exist()
    {
        return $this->_info && app()->getModule($this->_info['name']) ? TRUE : FALSE;
    }

    /**
     * @param string $func
     * @param null $param
     * @return $this|bool|mixed
     */
    public function __call($func, $param = NULL)
    {
        if ($param) {
            if (isset($this->_info [$func][end($param)]) && $this->_info = $this->_info [$func][end($param)])
                return $this;
        } else {
            if (isset($this->_info [$func]) && $this->_info = $this->_info [$func])
                return $this;
        }

        return FALSE;
    }
}