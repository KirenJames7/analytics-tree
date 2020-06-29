<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('app.index');
});

Route::post('auth', 'LDAPController@userAuthentication')->middleware('ajax');

Route::get('checksession', 'LDAPController@userSessionCheck')->middleware('ajax');

Route::get('signout', 'LDAPController@userSessionDestroy')->middleware('ajax');

Route::get('import', 'FileUploadController@index')->middleware('ajax');

Route::get('currentuploadlist', 'FileUploadController@currentUploadList')->middleware('ajax');

Route::post('import', 'FileUploadController@uploadFile')->middleware('ajax');

Route::get('deleterecent', 'FileDeleteController@deleteData')->middleware('ajax');

Route::get('delete', 'FileDeleteController@index')->middleware('ajax');

Route::get('year', 'FileDeleteController@getYear')->middleware('ajax');

Route::get('month/{year}', 'FileDeleteController@getMonthsofYear')->middleware('ajax');

Route::post('delete', 'FileDeleteController@deleteData')->middleware('ajax');

Route::get('getlatestperiod', 'ReportController@getLatestPeriod')->middleware('ajax');

Route::get('getreportfilters/{field}', 'ReportController@reportFilters')->middleware('ajax');

Route::get('getrolescope', 'ReportController@reportScope')->middleware('ajax');

Route::get('getmax', 'ReportController@getMax')->middleware('ajax');

Route::get('utilizationsummary', 'ReportController@utilizationSummary')->middleware('ajax');

Route::get('getreportdatautilization-summary/{hodname?}/{year?}/{month?}/{ptype?}/{pcode?}/{rname?}', 'ReportController@utilizationSummaryData', function ($hodname = [], $year = [], $month = [], $ptype = [], $pcode = [], $rname = []){
    return array($hodname, $year, $month, $ptype, $pcode, $rname);
})->middleware('ajax');

Route::get('associateutilizationsummary', 'ReportController@associateUtilizationSummary')->middleware('ajax');

Route::get('getreportdataassociate-utilization-summary/{hodname?}/{year?}/{month?}/{ptype?}/{pcode?}/{rname?}', 'ReportController@associateUtilizationSummaryData', function ($hodname = [], $year = [], $month = [], $ptype = [], $pcode = [], $rname = []){
    return array($hodname, $year, $month, $ptype, $pcode, $rname);
})->middleware('ajax');

Route::get('admin', 'AdminController@index')->middleware('ajax');

Route::get('rolemanagement', 'AdminController@rolemanagement')->middleware('ajax');

Route::get('systemlogs', 'AdminController@systemlogs')->middleware('ajax');

Route::get('getbus', 'AdminController@getBUs')->middleware('ajax');

Route::get('rolename', 'AdminController@roleNameValidation')->middleware('ajax');

Route::get('currentroles', 'AdminController@currentRoles')->middleware('ajax');

Route::get('getusers', 'LDAPController@userRegistration')->middleware('ajax');

Route::post('addrole', 'AdminController@addRole')->middleware('ajax');

Route::get('deleterole', 'AdminController@deleteRole')->middleware('ajax');

Route::post('roleadduser', 'AdminController@roleAddUser')->middleware('ajax');

Route::post('roledeleteuser', 'AdminController@roleDeleteUser')->middleware('ajax');

Route::post('rolemodifyscope', 'AdminController@roleModifyScope')->middleware('ajax')
        ;
Route::post('rolemodifysystemadmin', 'AdminController@roleModifySystemAdmin')->middleware('ajax')
        ;
Route::post('rolemodifyfilemanager', 'AdminController@roleModifyFileManager')->middleware('ajax');

Route::post('rolemodifyallbuaccess', 'AdminController@roleModifyAllBUAccess')->middleware('ajax');

Route::get('getmodifiedrole', 'AdminController@getModifiedRole')->middleware('ajax');