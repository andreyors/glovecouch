<?php

include 'vendor/autoload.php';

include 'lib/common.php';

$app = new \Slim\Slim(array(
    'debug' => true,
    'view' => new \Slim\Views\Twig,
    'templates.path' => APP_ROOT . '/views',
  )
);

$view = $app->view;
$view->parserOptions = array(
    'debug' => true,
    'cache' => APP_ROOT . '/cache',
);

$app->get('/', function() use ($app) {
  $app->render('frontend/index/index.twig');
}); 

$app->get('/api/start', 'API', function() use ($app) {
  $app->render(200, ['api' => 'start']);
});

$app->get('/api/stop', 'API', function() use ($app) {
  $app->render(200, ['api' => 'stop']);
});

$app->get('/api/info', 'API', function() use ($app) {
  $app->render(200, ['api' => 'info']);
});

$app->get('/api/replay', 'API', function() use ($app) {
  $app->render(200, ['api' => 'replay']);
});

$app->run();