<?php

require_once __DIR__ . '/vendor/autoload.php';

use Fenris\Analyzer;

$cwd = rtrim(dirname(__FILE__), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
$analyzer = new Analyzer($cwd . 'cache');
$analyzer
    ->analyze($cwd . 'examples')    ## remove this line or delete the files in the examples Folder
    ->analyze('F:\\Battle.net\\Diablo IV')
    ->writeFiles($cwd . 'output');


