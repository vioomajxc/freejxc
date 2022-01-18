<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitcbf1b7b3c027ae845469c47696dad75d
{
    public static $prefixLengthsPsr4 = array (
        't' => 
        array (
            'think\\worker\\' => 13,
            'think\\composer\\' => 15,
        ),
        'a' => 
        array (
            'app\\' => 4,
        ),
        'W' => 
        array (
            'Workerman\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'think\\worker\\' => 
        array (
            0 => __DIR__ . '/..' . '/topthink/think-worker/src',
        ),
        'think\\composer\\' => 
        array (
            0 => __DIR__ . '/..' . '/topthink/think-installer/src',
        ),
        'app\\' => 
        array (
            0 => __DIR__ . '/../..' . '/application',
        ),
        'Workerman\\' => 
        array (
            0 => __DIR__ . '/..' . '/workerman/workerman',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitcbf1b7b3c027ae845469c47696dad75d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitcbf1b7b3c027ae845469c47696dad75d::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}