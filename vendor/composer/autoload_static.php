<?php

namespace Composer\Autoload;

class ComposerStaticInit5249d7fdb4a33a43853768718981554e
{
    public static $prefixLengthsPsr4 = array(
        'P' =>
        array(
            'IslamAlsayed\\Paymob\\' => 7,
            'IslamAlsayed\\PayMob\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array(
        'IslamAlsayed\\Paymob\\' =>
        array(
            0 => __DIR__ . '/../..' . '/paymob',
        ),
        'IslamAlsayed\\PayMob\\' =>
        array(
            0 => __DIR__ . '/../..' . '/paymob/laravel',
        ),
    );

    public static $classMap = array(
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit5249d7fdb4a33a43853768718981554e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit5249d7fdb4a33a43853768718981554e::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit5249d7fdb4a33a43853768718981554e::$classMap;
        }, null, ClassLoader::class);
    }
}
