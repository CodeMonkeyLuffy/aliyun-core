<?php

namespace CodeMonkeyLuffy\Aliyun\Core\Autoloader;

spl_autoload_register('Autoloader::autoload');

class Autoloader
{
    /**
     * @var array
     */
    private static $autoloadPathArray = array(
        'core',
        'core/Auth',
        'core/Http',
        'core/Profile',
        'core/Regions',
        'core/Exception',
    );

    /**
     * Automatically find the class and load it.
     *
     * @param string $className
     */
    public static function autoload($className)
    {
        $directories = dirname(dirname(__DIR__));
        foreach (self::$autoloadPathArray as $path) {
            $file = $directories . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $className . '.php';
            $file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
            if (is_file($file)) {
                include_once $file;
                break;
            }
        }
    }

    /**
     * Load all product folders.
     *
     * @return void
     */
    public static function loadDirectories()
    {
        $directories = dirname(dirname(__DIR__));
        foreach (glob($directories . DIRECTORY_SEPARATOR . '*') as $directory) {
            if (is_dir($directory) && basename($directory) !== 'core') {
                self::$autoloadPathArray[] = basename($directory);
            }
        }
    }

    /**
     * @param string $path
     */
    public static function addAutoloadPath($path)
    {
        self::$autoloadPathArray[] = $path;
    }
}
