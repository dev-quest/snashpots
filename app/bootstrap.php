<?php
date_default_timezone_set('UTC');

$root = '..';

require "$root/vendor/autoload.php";

// Prepare app
$app = new \Slim\Slim(array(
    'templates.path' => "$root/templates",
    'log.level' => \Slim\Log::WARN,
    'log.enabled' => true,
    'log.writer' => new \Slim\Extras\Log\DateTimeFileWriter(array(
        'path' => "$root/logs",
        'name_format' => 'y-m-d'
    ))
));

// Prepare view
\Slim\Extras\Views\Twig::$twigOptions = array(
    'charset' => 'utf-8',
    'cache' => realpath("$root/cache"),
    'auto_reload' => true,
    'strict_variables' => false,
    'autoescape' => true
);
$app->view(new \Slim\Extras\Views\Twig());

// Define routes
$app->get('/', function () use ($app) {
    $app->render('index.html');
});

// Run app
$app->run();
