<?php

include 'conf/config.php';

function API() {
  $app = \Slim\Slim::getInstance();
  $app->add(new \SlimJson\Middleware([
    'json.status' => true,
    'json.debug' => false, 
    'json.override_error' => true,
    'json.override_notfound' => true
  ]));
}
