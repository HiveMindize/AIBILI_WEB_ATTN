<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3a50296529cc7674da7bfbc67e46d74c
{
    public static $prefixLengthsPsr4 = array (
        'Y' => 
        array (
            'Yasumi\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Yasumi\\' => 
        array (
            0 => __DIR__ . '/..' . '/azuyalabs/yasumi/src/Yasumi',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3a50296529cc7674da7bfbc67e46d74c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3a50296529cc7674da7bfbc67e46d74c::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
