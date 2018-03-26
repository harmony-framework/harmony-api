<?php
// DIC configuration
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// database
$container['db'] = function ($c) {
    $settings = $c->get('settings')['db'];
    $db = Database::get($settings);
    return $db;
};

// mailing
$container['mailer'] = function ($c) {
    $settings = $c->get('settings')['mail'];
    return new Mail($settings);
};
// custom error handler
$container['errorHandler'] = function ($c, $message = "Generic error message", $status = 200) {
    return new ErrorHandler($message, $status);
};

// authentication 
$container['authentication'] = function ($c) {
    return  $c->get('settings')['authentication'];
};