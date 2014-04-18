<?php
/**
 * Created by PhpStorm.
 * User: Ali Rajabi - www.alirajabi.com
 * Date: 10/28/13
 * Time: 2:43 PM
 */

namespace rajabi\helper;

/**
 * This is the shortcut to DIRECTORY_SEPARATOR
 */
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

/**
 * Class RFunc
 * @package rajabi\helper
 * Common functions
 */
class RFunc
{
    /**
     * Truncate words
     * @param $statement
     * @param $max
     * @return string
     */
    public static function truncate($statement, $max)
    {
        if (mb_strlen($statement, 'utf-8') >= $max) {
            $statement = mb_strimwidth($statement, 0, $max - 3, '...', 'utf-8');
        }
        return $statement;
    }

    /**
     * var_dump Data
     * @param $a
     */
    public static function dump($a)
    {
        header('Content-Type: text/html; charset=utf-8');
        var_dump($a);
        exit;
    }

    /**
     * Base64 & serialize data
     * @param $data
     * @return string
     */
    public static function b64Serialize($data)
    {
        return base64_encode(serialize($data));
    }

    /**
     * Base64 & serialize data
     * @param $data
     * @return mixed
     */
    public static function b64UnSerialize($data)
    {
        return unserialize(base64_decode($data));
    }

    /**
     * Get N level arrays to one array
     * @param array $data
     * @param $result
     * @param array $ignoreKey
     * @return array
     */
    public static function returnArraysRecursive(array $data, &$result, array $ignoreKey)
    {
        if (is_null($result)) $result = array();

        foreach ($data as $key => $val) {

            if (in_array($key, $ignoreKey))
                continue;

            if (is_string($key))
                $result [$key] = 1;

            if (is_bool($val))
                continue;

            if (!is_array($val)) {
                $result [$val] = 1;
            } else {
                self::returnArraysRecursive($val, $result, $ignoreKey);
            }
        }
        return $result;
    }
}

/**
 * Class RFile
 * @package rajabi\helper
 *
 */
class RFile
{

    /**
     * Get sub directories
     * @param $path
     * @return array|bool|null
     */
    public static function getDirectories($path)
    {

        $directories = @scandir($path);
        if ($directories) {
            $result = array();
            foreach ($directories as $directory) {
                if ($directory == '.' || $directory == '..' || strtolower($directory) == 'thumbs.db')
                    continue;
                if (is_readable($path . DS . $directory) && is_dir($path . DS . $directory))
                    $result[] = $path . DS . $directory;
            }
            return $result;
        }
        return null;
    }

    /**
     * Get directory files
     * @param $path
     * @return array|bool|null
     */
    public static function getFiles($path)
    {

        $files = @scandir($path);

        if ($files) {
            $result = array();
            foreach ($files as $file) {
                if ($file == '.' || $file == '..' || strtolower($file) == 'thumbs.db')
                    continue;
                if (is_readable($path . DS . $file))
                    if (is_dir($path . DS . $file))
                        $result[] = self::getFiles($path . DS . $file);
                    else
                        $result[] = $path . DS . $file;


            }
            return $result;
        }
        return null;
    }

    /**
     * Delete a file
     * @param $path
     * @return bool
     */
    public static function deleteFile($path)
    {
        if (file_exists($path))
            if (@unlink($path)) {
                return TRUE;
            } else {
                RException::save($path . "\t delete file failed");
                return FALSE;
            }
        else
            return FALSE;
    }

    /**
     * Delete a directory
     * @param $path
     * @return bool
     */
    public static function removeDir($path)
    {

        if (file_exists($path) && is_dir($path) && is_readable($path)) {

            try {
                $contents = scandir($path);
            } catch (RException $e) {
                RException::save($e);
                return FALSE;
            }
            foreach ($contents as $content) {
                if (in_array($content, array(
                    '.',
                    '..'))
                )
                    continue;
                if (is_dir($path . '/' . $content)) {
                    self::removeDir($path . '/' . $content);
                    rmdir($path . '/' . $content);
                } else
                    self::deleteFile($path . '/' . $content);
            }
            if (rmdir($path)) {
                return TRUE;
            } else {
                RException::save($path . "\t delete path failed");
                return FALSE;
            }
        } else {
            RException::save($path . "\t directory not exist");
            return FALSE;
        }
    }

    /**
     * Make a directory
     * @param $dir
     * @return bool
     */
    public static function makeDir($dir)
    {
        if (file_exists($dir) && !is_readable($dir)) {
            if (@chmod($dir, 0777)) {
                return TRUE;

            } else {
                RException::save($dir . "\t permission denied");
                return FALSE;

            }
        }
        if (!file_exists($dir)) {
            if (@mkdir($dir, 0777)) {
                return TRUE;
            } else {
                RException::save($dir . "\t permission denied");
                return FALSE;
            }


        }

        return TRUE;

    }

    /**
     * Get File Content
     * @param $path
     * @param bool $return
     * @param string $separator
     * @return array|bool|string
     */
    public static function getFile($path, $return = FALSE, $separator = ";")
    {

        if (!file_exists($path) || !is_readable($path)) {
            RException::save($path . "\t" . 'ERROR FILE NOT EXISTS.');
            return FALSE;
        }
        if (TRUE === $return)
            return explode($separator, file_get_contents($path));
        else
            return file_get_contents($path);

    }

    /**
     * Write data to file
     * @param $path
     * @param $content
     * @param bool $rewrite
     * @return bool
     */
    public static function writeFile($path, $content, $rewrite = FALSE)
    {
        if (file_put_contents($path, $content, $rewrite ? NULL : FILE_APPEND)) {
            return TRUE;
        } else {
            RException::save($path . "t\ write file failed");
            return FALSE;
        }
    }

}

/**
 * Class RFileCache
 * @package rajabi\helper
 * File cache functions
 */
class RFileCache
{
    static $time = 86400;

    static $directory;

    /**
     * Save data to cache
     * @param $key
     * @param $data
     * @return bool
     */
    public static function set($key, $data)
    {
        //Check if dir not exists
        RFile::makeDir(self::$directory);
        try {
            RFile::writeFile(self::$directory . DS . md5($key) . '.rCache', RFunc::b64Serialize($data));
        } catch (RException $e) {
            RException::save($e);
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Get data from cache
     * @param $key
     * @return bool|mixed
     */
    public static function get($key)
    {
        $key = self::$directory . DS . md5($key) . '.rCache';
        if (file_exists($key) && is_readable($key) && filemtime($key) + self::$time > time()) {
            try {
                return RFunc::b64UnSerialize(RFile::getFile($key));
            } catch (RException $e) {
                RException::save($e);
                return FALSE;
            }

        } else {
            RFile::deleteFile($key);
        }
        return FALSE;

    }
}

/**
 * Class RException
 * @package rajabi\helper
 */
class RException extends \Exception
{
    private static $_errors = array();

    /**
     * @param string $msg
     */
    public function __construct($msg)
    {
        parent::__construct($msg);
    }

    /**
     * @param $RException
     */
    public static function save($RException)
    {
        self::$_errors[] = $RException;
    }

    /**
     * Get errors
     * @return array|bool
     */
    public static function get()
    {
        if (array_filter(self::$_errors))
            return self::$_errors;
        else
            return FALSE;
    }

    /**
     * Truce errors
     * @return bool
     */
    public static function trace()
    {
        $html = array();
        if (array_filter(self::$_errors)) {
            foreach (self::$_errors as $error) {
                echo '<div>' . $error . '</div>';
            }
            exit();
        } else
            return FALSE;
    }

    /**
     * Delete errors
     */
    public static function flush()
    {
        self::$_errors = array();
    }
}

