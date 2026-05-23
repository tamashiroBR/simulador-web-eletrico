<?php
// PHP 8 compatible: uses sequential LoadFlow instead of LoadFlowT (which requires pthreads extension)
//header('Content-Type: application/json; charset=utf-8');
//set_time_limit(0);
require 'bootstrap.php';

use NDSE\Tools\LoadFlow;

if (!is_null($data)) {

    $lf = new LoadFlow($data);
    $lf->makeYbus();
    $result = $lf->run();

    echo $result;

}
