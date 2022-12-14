<?php
$namespace = 'fluxlabs\learnplaces';
$baseDirectory = __DIR__ . '/src';

spl_autoload_register(function (string $class) use ($namespace, $baseDirectory) {
    $classNameParts = explode($namespace, $class);
    // not our responsibility
    if (count($classNameParts) !== 2) {
        return;
    }
    $filePath = str_replace('\\', '/', $classNameParts[1]) . '.php';
    require $baseDirectory . $filePath;
});