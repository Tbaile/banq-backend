<?php

$finder = PhpCsFixer\Finder::create()
    ->in(['app', 'tests']);

$config = new \PhpCsFixer\Config();
return $config
    ->setRules([
        '@PSR12' => true
    ])
    ->setFinder($finder);
