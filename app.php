<?php

require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Finder\Finder;

/**
 * auto load the classes defined in Command directory.
 *
 * @param Object $app
 */
function registerCommand($app)
{
    $finder = new Finder();
    $finder->files()->name('*.php')->in(__DIR__ . '/src/Command');

    foreach ($finder as $file) {
        $filename = $file->getFilename();
        if (!preg_match("/Command.php$/", $filename)) {
            continue;
        }
        try {
            $cmdClass = new \ReflectionClass('\\Wusuopu\\Command\\' . substr($filename, 0, strlen($filename) - 4));
            $cmd = $cmdClass->newInstanceArgs();
            $app->add($cmd);
        } catch (\ReflectionException $e) {
        }
    }
}

$application = new Application();

registerCommand($application);

$application->run();
