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

Route::get('/', 'HomeController@index');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => ['auth']], function () 
{
Route::resource('agents', 'agentcontroller\AgentsController');
Route::get('/agents/reports/all', 'agentcontroller\AgentsController@report');
Route::resource('clients', 'clientcontroller\ClientController');
Route::resource('tasks', 'taskcontroller\TaskController');
Route::get('tasks/completed/all', 'taskcontroller\TaskController@completed');

Route::post('/task/meeting/remember', 'taskcontroller\TaskController@remember');

Route::resource('invoices', 'invoicecontroller\InvoiceController');
Route::get('/invoices/print/{id}', 'invoicecontroller\InvoiceController@print');
Route::get('/invoices/pdf/{id}', 'invoicecontroller\InvoiceController@pdf');
Route::post('/task/data/get', 'invoicecontroller\InvoiceController@taskdata');
Route::resource('agentinvoices', 'invoicecontroller\AgentinvoiceController');

Route::post('/invoices/print/agent', 'invoicecontroller\AgentinvoiceController@print');
Route::post('/invoices/sendinvoice/agent', 'invoicecontroller\AgentinvoiceController@sendemail');
Route::post('agentinvoice/pdf/agent/download', 'invoicecontroller\AgentinvoiceController@downloadpdf');

Route::get('/theme/setting', 'themesetting\SettingController@index');
Route::post('/theme/setting', 'themesetting\SettingController@setting');
Route::get('/task/completed/{date}', 'taskcontroller\TaskgraphController@index');
Route::get('/task/created_at/{date}', 'taskcontroller\TaskgraphController@createdatIndex');
Route::get('/task/completed/{date}/{id}', 'taskcontroller\TaskgraphController@agentindex');
Route::get('/report/list', 'reportcontroller\ReportController@index');

Route::post('/export/to/excel', 'taskcontroller\TaskgraphController@exporttoexcel');
});

Route::post('/emaillink', 'Auth\ForgotPasswordController@sendemail');
Route::get('/invoices/{date}/download/agent/{dia_ids}', 'invoicecontroller\AgentinvoiceController@download')->where(['dia_ids'=>'.*']);;



Route::get('/team/login', 'team\TeamController@index');
Route::post('/team/logout', 'team\TeamController@logout');
Route::post('/team/login', 'team\TeamController@check');
Route::get('/team/password/reset', 'team\TeamController@forgot');
Route::get('/passwordresetlink/{token}/{email}', 'Auth\ForgotPasswordController@passwordreset');
Route::post('/password/reset/now', 'Auth\ForgotPasswordController@resetcommit');
Route::get('/team/password', 'team\TeamtaskController@password2');
Route::post('/team/passupdate', 'team\TeamtaskController@passupdate');



Route::group(['middleware' => ['checkagent']], function () 
{
Route::get('/team/{id}/task/created_at/{date}', 'team\TeamController@newtasksIndex');
Route::get('/team/tasks/{id}', 'team\TeamtaskController@show');

});
Route::post('/teamtask/update', 'team\TeamtaskController@update');
