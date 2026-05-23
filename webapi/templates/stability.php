<?php 
//header('Content-Type: application/json; charset=utf-8');
//set_time_limit(0);
require 'bootstrap.php';

use NDSE\Tools\TransientAnalysis;

if (!is_null($data)) {
	
	$ta = new TransientAnalysis($data);
    $result = $ta->run();

	echo $result;

}