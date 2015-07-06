<?php

/*
 *
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('api/{channel}/{date?}', 'ApiController@showChannel')
    ->where('date', '[0-9]{4}-[0-1][0-9]-[0-3][0-9]/[0-2][0-9]:[0-5][0-9]');

Route::get('/{any}', 'LogsController@index')
    ->where('any', '(.*)');

/*
Route::get('{channel}/{date?}', 'LogsController@showChannel')
    ->where('date', '[0-9]{4}-[0-1][0-9]-[0-3][0-9](/[0-2][0-9]:[0-5][0-9])?');

Route::get('{channel}/search/', 'LogsController@search');
Route::get('{channel}/search/{query?}', 'LogsController@search');

Route::get('{channel}/infinite/{direction}/{id}', 'LogsController@infinite');
*/
