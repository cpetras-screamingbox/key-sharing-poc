<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', "SiteController@index");
$router->get('/admin', "SiteController@admin");
$router->get('/client', "SiteController@client");

$router->get('/api/datarequest', "SiteController@getAllDatarequest");
$router->get('/api/datarequest/{id}', "SiteController@getDatarequest");
$router->put('/api/datarequest/{id}', "SiteController@updateDatarequest");
$router->post('/api/datarequest', "SiteController@createDataRequest");

$router->get('/api/admin', "SiteController@getAllAdmin");
$router->get('/api/admin/{id}', "SiteController@getAdmin");
$router->post('/api/admin', "SiteController@createAdmin");
