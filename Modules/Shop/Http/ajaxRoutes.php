<?php

use Illuminate\Routing\Router;

/** @var Router $router */
if (! App::runningInConsole()) {
    $router->get('/API/muestraCarroAjax', 'PublicController@muestraCarroAjax');
}