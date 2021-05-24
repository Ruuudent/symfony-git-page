<?php

// config/routes.php
use App\Controller\PageController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->add('__index', '/')->controller([PageController::class, 'index'])->methods(['GET']);
    $routes->add('__user', '/user')->controller([PageController::class, 'getUserData'])->methods(['POST']);
};
