<?php

$sep = DIRECTORY_SEPARATOR;
$root = realpath(__DIR__ . $sep . '..' . $sep . '..');

$finder = PhpCsFixer\Finder::create()
    ->in($root . $sep . 'src');

$config = new PhpCsFixer\Config();

return $config
    ->setRules([
        '@Symfony' => true,
    ])
    ->setFinder($finder);
