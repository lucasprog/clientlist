<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () use ($router) {
    abort(403,"Não autorizado!");
});


$router->group(['prefix' => 'api'], function () use ($router) {

    $router->post('/login', "LoginController@sigIn");

    $router->group(['middleware' => 'auth'], function () use ($router) {
        $router->group(['prefix' => 'client'], function () use ($router) {
            $router->get('/read',"ClientsController@read");
            $router->post('/store', "ClientsController@store");
            $router->put('/update', "ClientsController@update");
            $router->delete('/delete/{client_id}', "ClientsController@delete");
        });
    });

});

