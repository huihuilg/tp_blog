<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit341656d7d6c4969bffe3f65f33e3b7a1
{
    public static $files = array (
        'ad155f8f1cf0d418fe49e248db8c661b' => __DIR__ . '/..' . '/react/promise/src/functions_include.php',
        'e8aa6e4b5a1db2f56ae794f1505391a8' => __DIR__ . '/..' . '/amphp/amp/lib/functions.php',
        'decc78cc4436b1292c6c0d151b19445c' => __DIR__ . '/..' . '/phpseclib/phpseclib/phpseclib/bootstrap.php',
        '8592c7b0947d8a0965a9e8c3d16f9c24' => __DIR__ . '/..' . '/elasticsearch/elasticsearch/src/autoload.php',
    );

    public static $prefixLengthsPsr4 = array (
        't' => 
        array (
            'think\\composer\\' => 15,
        ),
        'p' => 
        array (
            'phpseclib\\' => 10,
        ),
        'a' => 
        array (
            'app\\' => 4,
        ),
        'R' => 
        array (
            'React\\Promise\\' => 14,
        ),
        'P' => 
        array (
            'Psr\\Log\\' => 8,
            'PhpAmqpLib\\' => 11,
            'PHPMailer\\PHPMailer\\' => 20,
        ),
        'G' => 
        array (
            'GuzzleHttp\\Stream\\' => 18,
            'GuzzleHttp\\Ring\\' => 16,
        ),
        'E' => 
        array (
            'Elasticsearch\\' => 14,
        ),
        'A' => 
        array (
            'Amp\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'think\\composer\\' => 
        array (
            0 => __DIR__ . '/..' . '/topthink/think-installer/src',
        ),
        'phpseclib\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpseclib/phpseclib/phpseclib',
        ),
        'app\\' => 
        array (
            0 => __DIR__ . '/../..' . '/application',
        ),
        'React\\Promise\\' => 
        array (
            0 => __DIR__ . '/..' . '/react/promise/src',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
        'PhpAmqpLib\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-amqplib/php-amqplib/PhpAmqpLib',
        ),
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
        'GuzzleHttp\\Stream\\' => 
        array (
            0 => __DIR__ . '/..' . '/ezimuel/guzzlestreams/src',
        ),
        'GuzzleHttp\\Ring\\' => 
        array (
            0 => __DIR__ . '/..' . '/ezimuel/ringphp/src',
        ),
        'Elasticsearch\\' => 
        array (
            0 => __DIR__ . '/..' . '/elasticsearch/elasticsearch/src/Elasticsearch',
        ),
        'Amp\\' => 
        array (
            0 => __DIR__ . '/..' . '/amphp/amp/lib',
        ),
    );

    public static $prefixesPsr0 = array (
        'K' => 
        array (
            'Kafka\\' => 
            array (
                0 => __DIR__ . '/..' . '/nmred/kafka-php/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit341656d7d6c4969bffe3f65f33e3b7a1::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit341656d7d6c4969bffe3f65f33e3b7a1::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit341656d7d6c4969bffe3f65f33e3b7a1::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
