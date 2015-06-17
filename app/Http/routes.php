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

Route::get('/', 'HomeController@index');
Route::get('server', 'ServerReportingController@index');
Route::get('server/getEnvironments', 'ServerReportingController@getEnvironments');
Route::get('server/getServers', 'ServerReportingController@getServers');
Route::post('server/saveServerSet', 'ServerReportingController@saveServerSet');
Route::get('server/export', 'ServerReportingController@getCSV');
Route::get('app-status', 'APPStatusReportingController@index');
Route::get('app-status/getEnvironments', 'APPStatusReportingController@getEnvironments');
Route::get('app-status/getAPPStatuses', 'APPStatusReportingController@getAPPStatuses');
Route::post('app-status/saveAPPStatusSet', 'APPStatusReportingController@saveAPPStatusSet');
Route::get('app-status/export', 'APPStatusReportingController@getCSV');

// Route::resource('package', 'PackageController');
// Route::resource('upload', 'UploadController');

// Route::get('schedule', 'ScheduleController@index');
// Route::post('schedule', 'ScheduleController@getEventsByDateRange');

Route::get('tree', 'TreeController@index');
Route::get('tree/fetchAPPs', 'TreeController@getAPPIDsForDropDown');
Route::get('tree/fetchTree', 'TreeController@fetchTree');

Route::get('maintainServerReporting', 'ServerReportingController@index');
Route::get('maintainServerReporting/getEnvironments', 'ServerReportingController@getEnvironments');
Route::get('maintainServerReporting/getServers', 'ServerReportingController@getServers');
Route::post('maintainServerReporting/saveServerSet', 'ServerReportingController@saveServerSet');
Route::get('maintainServerReporting/export', 'ServerReportingController@getCSV');


Route::get('maintainAPPStatusReporting', 'APPStatusReportingController@index');
Route::get('maintainAPPStatusReporting/getEnvironments', 'APPStatusReportingController@getEnvironments');
Route::get('maintainAPPStatusReporting/getAPPStatuses', 'APPStatusReportingController@getAPPStatuses');
Route::post('maintainAPPStatusReporting/saveAPPStatusSet', 'APPStatusReportingController@saveAPPStatusSet');
Route::get('maintainAPPStatusReporting/export', 'APPStatusReportingController@getCSV');

// Route::controllers([
//     'auth' => 'Auth\AuthController',
//     'password' => 'Auth\PasswordController',
// ]);
