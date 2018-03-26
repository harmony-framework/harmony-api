<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Require routes 
require_once __DIR__ . '/routes/authentication.php';
require_once __DIR__ . '/routes/user.php';

// Routes
$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Landing API page '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});
