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

use Illuminate\Support\Facades\Log;

$router->group(['prefix' => 'api'], function ($router) {
    $router->get('test', function () {
        $msg = 'API Test Success!';
        Log::info($msg);
        return $msg;
    });

    $router->post('login', 'WisherController@login');
    $router->post('register', 'WisherController@register');

    $router->get('wishes', 'WishController@index');
    $router->post('wishes/search', 'WishController@search');

    $router->group(['middleware' => 'auth'], function ($router) {
        $router->get('wishers', 'WisherController@index');
        $router->get('wishers/{id}', 'WisherController@getUser');
        $router->post('wishers/{id}', 'WisherController@update');
        $router->delete('wishers/{id}', 'WisherController@delete');
        $router->delete('remove_profile_photo/{id}', 'WisherController@removeProfilePhoto');
        $router->get('wisher_wishes', 'WishController@getWishesForWisher');
        $router->post('wishes', 'WishController@addWish');
        $router->get('wishes/{id}', 'WishController@getWish');
        $router->post('wishes/{id}', 'WishController@update');
        $router->delete('wishes/{id}', 'WishController@delete');
    });
});
