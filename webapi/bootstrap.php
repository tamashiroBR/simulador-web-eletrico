<?php
//header('Content-Type: text/html; charset=utf-8');

require './src/NDSE/AutoLoader.php';

$loader = new Psr4AutoloaderClass;  

// register the autoloader
$loader->register();  

// register the base directories for the namespace prefix
$loader->addNamespace('NDSE', './src/NDSE/Core');
$loader->addNamespace('NDSE\Math', './src/NDSE/Core/Math');
$loader->addNamespace('NDSE\Tools', './src/NDSE/Core/Tools');
$loader->addNamespace('NDSE\Models', './src/NDSE/Core/Models');
$loader->addNamespace('NDSE\Models\Gen', './src/NDSE/Core/Models/Gen');