<?php

use Illuminate\Routing\Router;

/** @var Router $router */
if (! App::runningInConsole()) {
    $router->get('/shop/hosting/{id}', ['uses' => 'PublicController@getHosting', 'as' => 'getHosting']);
    $router->post('/shop/hosting/{id}', ['uses' => 'PublicController@postHosting', 'as' => 'postHosting']);
    $router->get('/shop/carro', ['uses' => 'PublicController@getCarro', 'as' => 'getCarro']);
    $router->delete('/shop/eliminar/{id}', ['uses' => 'PublicController@eliminar', 'as' => 'delete.item']);
    $router->get('/shop/checkout', ['uses' => 'PublicController@checkout', 'as' => 'checkout']);
    $router->get('/shop/pagar', ['uses' => 'PublicController@getPagar', 'as' => 'getPagar']);
    $router->post('/shop/pagar', ['uses' => 'PublicController@postPagar', 'as' => 'postPagar']);
    $router->get('/factura', ['uses' => 'FacturasController@invoice', 'as' => 'verFactura']);
    $router->post('/shop/comprobar/{dominio}', ['uses' => 'PublicController@comprobarDominio', 'as' => 'comprobar.dominio']);

    $router->get('/clientes', ['uses' => 'PrivateController@home', 'as' => 'private']);
    $router->get('/clientes/hosting', ['uses' => 'PrivateController@hosting', 'as' => 'private.hosting']);
    $router->get('/clientes/dominios', ['uses' => 'PrivateController@dominios', 'as' => 'private.dominios']);

}