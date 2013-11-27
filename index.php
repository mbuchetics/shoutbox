<?php
require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();

require_once 'server/functions.php';

$app = new \Slim\Slim(array(
    'templates.path' => './templates'
));

$app->post('/post', function () {
    postMessage($_POST['text']);
});

$app->get('/list', function () use ($app) {
    $messages = getMessages();

    $app->response()->header('Content-Type', 'application/json');
    echo json_encode($messages);
});

$app->get('/last', function () use ($app) {
    $message = getLastMessage();
    if (!isset($message)) $message = '';

    $app->response()->header('Content-Type', 'application/json');
    echo json_encode($message);
});

$app->post('/upload/', function() use ($app) {
    $tmp = $_FILES['file']['tmp_name'];
    $name = $_FILES['file']['name'];
    
    saveImage($tmp, $name, 340);
});

$app->get('/', function () use ($app) {
    $app->render('index.html');
});

$app->run();