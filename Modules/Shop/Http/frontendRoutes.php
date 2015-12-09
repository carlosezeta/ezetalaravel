<?php

use Illuminate\Routing\Router;

/** @var Router $router */
if (! App::runningInConsole()) {
    $router->get('/shop/hosting/{id}', ['uses' => 'PublicController@getHosting', 'as' => 'getHosting']);
    $router->post('/shop/hosting/{id}', ['uses' => 'PublicController@postHosting', 'as' => 'postHosting']);
    $router->get('/shop/carro', ['uses' => 'PublicController@getCarro', 'as' => 'getCarro']);
    $router->delete('/shop/eliminar/{id}', ['uses' => 'PublicController@eliminar', 'as' => 'delete.item']);
    $router->get('/shop/checkout', ['uses' => 'PublicController@checkout', 'as' => 'checkout']);
}