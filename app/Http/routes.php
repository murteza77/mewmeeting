<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::group(['middleware' => 'guest'], function(){
    Route::get('/', function()
    {
        return View::make('landingPage');
    });

});
Route::controller('account','AccountController');

Route::group(['middleware' => 'auth'], function(){
    Route::controller('user','UserController');
});

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');