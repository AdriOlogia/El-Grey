<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7a576dfa1901c5a276bc169a83e58f88
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7a576dfa1901c5a276bc169a83e58f88::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7a576dfa1901c5a276bc169a83e58f88::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
