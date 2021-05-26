<?php

// config/routes.php
use App\Controller\PageController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    // Routes could be expanded to just input the username and auto search by that
    $routes->add('__index', '/')->controller([PageController::class, 'index'])->methods(['GET']);
    $routes->add('__user', '/user' /* {/user} */)->controller([PageController::class, 'getUserData'])->methods(['POST']);
};
