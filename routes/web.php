<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/;

$router->get('/', function () use ($router) {
	return $router->app->version();
});

$router->group(['prefix' => 'api'], function() use ($router){
	$router->group(['prefix' => 'customers'], function() use ($router){
		$router->post('create', ['uses' => 'CustomerController@create']);
		$router->post('login', ['uses' => 'CustomerController@login']);

		$router->get('show', [
			'middleware' => 'token',
			'uses' => 'CustomerController@show'
		]);
		$router->post('edit', [
			'middleware' => 'token',
			'uses' => 'CustomerController@edit'
		]);
	});

	$router->group(['prefix' => 'tasks', 'middleware' => 'token'], function() use ($router){
		$router->post('create', ['uses' => 'TaskController@create']);
		$router->get('list', ['uses' => 'TaskController@list']);
		$router->post('edit', ['uses' => 'TaskController@edit']);
		$router->post('delete', ['uses' => 'TaskController@delete']);
	});
});
