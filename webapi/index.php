<?php
require 'Slim\Slim.php';

use Slim\Slim;
Slim::registerAutoloader();

$app = new Slim();

$app->config(
    [
        'templates.path' => 'templates'
    ]
);

//Defines routes
$app->get('/', function () use ($app){ 
	echo "<h1>NDSE Web Simulator web API</h1>";
}); 

$app->group('/nws/v1',function() use ($app){
	
	// $app->get('/loadflow', function () use ($app){
		// echo "<h1>NDSE Web Simulator web API</h1>";
		// echo "<h2>Run Load Flow</h2>";
		// echo "<h3>(only POST method)</h3>";	
	// }); 

	$app->post('/loadflow',function() use ($app){ 
		$json =  json_decode($app->request->getBody());
		$data = ['data'=>[
							'optLF' => $json->optLF,
							'bus' => $json->bus,
							'branch' => $json->branch
						 ]
				];
		$app->response()->header("Content-Type", "application/json");
		$app->render('loadflow.php', $data, 200);
	});
	
	// $app->get('/stability', function () use ($app){
		// echo "<h1>NDSE Web Simulator web API</h1>";
		// echo "<h2>Run Transient Stability Analysis</h2>";
		// echo "<h3>(only POST method)</h3>";	
	// }); 
	
	$app->post('/stability',function() use ($app){ 
		$json =  json_decode($app->request->getBody());
		$data = ['data'=>[
							'optLF' => $json->optLF,
							'optTA' => $json->optTA,
							'bus' => $json->bus,
							'branch' => $json->branch,
							'gen' => $json->gen,
							'exc' => $json->exc,
							'gov' => $json->gov,
							'event' => $json->event						
						 ]
				];
		$app->response()->header("Content-Type", "application/json");
		$app->render('stability.php', $data, 200);
	});

});
 
//Run Slim application
$app->run();