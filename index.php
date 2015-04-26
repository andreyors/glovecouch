<?php

include 'vendor/autoload.php';
include 'lib/common.php';

$app = new \Slim\Slim(array(
    'debug' => true,
    'view' => new \Slim\Views\Twig,
    'templates.path' => APP_ROOT . '/views',
));

$db = DB(DB_DSN, DB_USER, DB_PASS);

$view = $app->view;
$view->parserOptions = array(
    'debug' => true,
    'cache' => APP_ROOT . '/var/cache',
);

$app->get('/', function() use ($app, $db) {
  $app->render('index/index.twig');
});

$app->get('/my', function() use ($app, $db) {
  $sql = "SELECT COUNT(1) cnt FROM (SELECT a1.ts, a1.z, MAX(a2.z) highest FROM gc_events a1 LEFT JOIN gc_events a2 ON a2.ts BETWEEN DATE_SUB(a1.ts, INTERVAL 15 SECOND) AND DATE_ADD(a1.ts, INTERVAL 15 SECOND) GROUP BY a1.ts, a1.z HAVING a1.z = highest ORDER BY a1.ts) max";
  $res = $db->query($sql);
  
  $cnt = $res ? $res->fetchColumn() : 0;
  
  $app->render('index/my.twig', array('cnt' => $cnt));
}); 

$app->get('/workout', function() use ($app, $db) {
  $res = $db->query("SELECT val_int FROM gc_switches WHERE name = 'like'");
  $like = $res->fetchColumn();
  
  $sql = "SELECT COUNT(1) cnt FROM (SELECT a1.ts, a1.z, MAX(a2.z) highest FROM gc_events a1 LEFT JOIN gc_events a2 ON a2.ts BETWEEN DATE_SUB(a1.ts, INTERVAL 15 SECOND) AND DATE_ADD(a1.ts, INTERVAL 15 SECOND) GROUP BY a1.ts, a1.z HAVING a1.z = highest ORDER BY a1.ts) max";
  $res = $db->query($sql);
  
  $cnt = $res ? $res->fetchColumn() : 0;
  
  $app->render('index/workout.twig', array('like' => intval($like), 'cnt' => $cnt ));
});

$app->get('/like', function() use ($app, $db) {
  $db->exec("UPDATE gc_switches SET val_int = 1 WHERE name = 'like'");
});

$app->get('/ajax/like', function() use ($app, $db) {
  $db->exec("UPDATE gc_switches SET val_int = 1 WHERE name = 'like'");
});

$app->get('/api/clinch', 'API', function() use ($app) {
  $app->render(200, ['times' => '10', 'sets' => '3', 'pause' => '20']);
});

$app->get('/api/progress', 'API', function() use ($app, $db) {
  $sql = "SELECT COUNT(1) cnt FROM (SELECT a1.ts, a1.z, MAX(a2.z) highest FROM gc_events a1 LEFT JOIN gc_events a2 ON a2.ts BETWEEN DATE_SUB(a1.ts, INTERVAL 15 SECOND) AND DATE_ADD(a1.ts, INTERVAL 15 SECOND) GROUP BY a1.ts, a1.z HAVING a1.z = highest ORDER BY a1.ts) max";
  $res = $db->query($sql);
  
  $cnt = $res ? $res->fetchColumn() : 0;
  
  $app->render(200, ['progress' => ceil($cnt * 100 / 30), 'cnt' => $cnt]);
});

$app->post('/api/poll', 'API', function() use ($app, $db) {
  $req = $app->request();
  
  $now = time();
  
  $x = $req->params('distX');
  $y = $req->params('distY');
  $z = $req->params('distZ');
  
  $tsdiff = 0;
  $row = [
      'x' => 0, 
      'y' => 0, 
      'z' => 0,
  ];
  
  
  $res = $db->query('SELECT x, y, z, ts FROM gc_events ORDER BY ts DESC LIMIT 1');
  
  if ($res) {
    $row = $res->fetch();
    $time = strtotime($row['ts']);
    $tsdiff = $now - $time;
  }
  
  
  
  if ( ($row['x'] != $x) && ($row['y'] != $y) && ($row['z'] != $z) ) {
    $sql = 'INSERT INTO gc_events (x, y, z, ts, tsdiff) VALUES (:x, :y, :z, :ts, :tsdiff)';
    $res = $db->prepare($sql);
    
    $ts = date('Y-m-d H:i:s', $now);
    
    $res->bindParam('x', $x);
    $res->bindParam('y', $y);
    $res->bindParam('z', $z);
    
    $res->bindParam('ts', $ts);
    $res->bindParam('tsdiff', $tsdiff);
    
    $res->execute();
  }
  
  $res = $db->query("SELECT val_int FROM gc_switches WHERE name = 'like'");
  $like = $res->fetchColumn();
  $db->exec("UPDATE gc_switches SET val_int = 0 WHERE name = 'like'");
  
  $app->render(200, ['like' => (intval($like) == 0 ? 'no' : 'yes') ]);
});

$app->run();