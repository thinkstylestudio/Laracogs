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

Route::group(['middleware' => 'web'], function() {

    /*
    |--------------------------------------------------------------------------
    | Welcome Page
    |--------------------------------------------------------------------------
    */

    Route::get('/', 'PagesController@home');

    /*
    |--------------------------------------------------------------------------
    | Login/ Logout/ Password
    |--------------------------------------------------------------------------
    */
    Route::get('login', 'Auth\AuthController@getLogin');
    Route::post('login', 'Auth\AuthController@postLogin');
    Route::get('logout', 'Auth\AuthController@getLogout');

    Route::get('password/email', 'Auth\PasswordController@getEmail');
    Route::post('password/email', 'Auth\PasswordController@postEmail');

    Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
    Route::post('password/reset', 'Auth\PasswordController@postReset');

    /*
    |--------------------------------------------------------------------------
    | Registration
    |--------------------------------------------------------------------------
    */
    Route::get('register', 'Auth\AuthController@getRegister');
    Route::post('register', 'Auth\AuthController@postRegister');

    /*
    |--------------------------------------------------------------------------
    | Authenticated Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['middleware' => 'auth'], function(){

        /*
        |--------------------------------------------------------------------------
        | User
        |--------------------------------------------------------------------------
        */

        Route::group(['prefix' => 'user', 'namespace' => 'User'], function(){
            Route::get('settings', 'SettingsController@settings');
            Route::post('settings', 'SettingsController@update');
            Route::get('password', 'PasswordController@password');
            Route::post('password', 'PasswordController@update');
        });

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', 'PagesController@dashboard');

        /*
        |--------------------------------------------------------------------------
        | Team Routes
        |--------------------------------------------------------------------------
        */

        Route::get('team/{name}', 'TeamController@showByName');
        Route::resource('teams', 'TeamController');
        Route::post('teams/search', 'TeamController@search');
        Route::get('teams/{id}/delete', [
            'as' => 'teams.delete',
            'uses' => 'TeamController@destroy',
        ]);
        Route::post('teams/{id}/invite', 'TeamController@inviteMember');
        Route::get('teams/{id}/remove/{userId}', 'TeamController@removeMember');

        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        */

        Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => 'admin'], function(){

            /*
            |--------------------------------------------------------------------------
            | Users
            |--------------------------------------------------------------------------
            */
            Route::resource('users', 'UserController', ['except' => ['create', 'show', 'destroy']]);
            Route::post('users/search', 'UserController@search');
            Route::get('users/search', 'UserController@index');
            Route::get('users/invite', 'UserController@getInvite');
            Route::post('users/invite', 'UserController@postInvite');
            Route::get('users/{id}/delete', 'UserController@destroy');
        });
    });

});
