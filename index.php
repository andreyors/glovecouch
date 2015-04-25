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
    'cache' => APP_ROOT . '/var/cache',
);

$app->get('/', function() use ($app) {
  $app->render('frontend/index/index.twig');
}); 

$app->get('/api/start', 'API', function() use ($app) {
  $app->render(200, ['times' => '10', 'sets' => '3', 'pause' => '20']);
});

$app->get('/api/poll', 'API', function() use ($app) {
  $app->render(200, ['process' => 'done', 'like' => 'yes']);
});

$app->run();