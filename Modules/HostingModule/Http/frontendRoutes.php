<?php

use Illuminate\Routing\Router;

/** @var Router $router */
if (! App::runningInConsole()) {
    $router->get('/prueba/{producto}', ['uses' => 'PublicController@contratahosting', 'as' => 'prueba']);
}
