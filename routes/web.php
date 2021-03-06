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
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/search', 'HomeController@search')->name('search');
Route::get('/currentinventory', 'UpdateController@currentInventory')->name('currentInventory');
Route::get('/purchaseordersummary', 'UpdateController@purchaseOrderSummary')->name('purchaseOrderSummary');
Route::get('/import', 'UpdateController@importData')->name('importData');
Route::get('/updatesalesmonth', 'UpdateController@updateSalesMonth')->name('updateSalesMonth');
Route::get('/updateunissued', 'UpdateController@updateUnissued')->name('updateUnissued');
//
Route::get('/monthlysales', 'HomeController@monthlySales')->name('monthlySales');
Route::get('/monthlysales/search', 'HomeController@monthlySalesSearch')->name('monthlySalesSearch');
//
Route::get('/weeklysales', 'HomeController@weeklySales')->name('weeklySales');
Route::get('/weeklysales/search', 'HomeController@weeklySalesSearch')->name('weeklySalesSearch');
//clearance 3 Months
Route::get('/clearance/3', 'HomeController@clearance3')->name('clearance3');
Route::get('/clearance/3/search', 'HomeController@clearance3Search')->name('clearance3Search');
//clearance 6 Months
Route::get('/clearance/6', 'HomeController@clearance6')->name('clearance6');
Route::get('/clearance/6/search', 'HomeController@clearance6Search')->name('clearance6Search');
//zerosales
Route::get('/zerosales', 'HomeController@zerosales')->name('zerosales');
Route::get('/zerosales/search', 'HomeController@zerosalesSearch')->name('zerosalesSearch');
//export to excel file
Route::get('/export/{report}', 'HomeController@exportToExcel')->name('exportToExcel');

Route::post('postDatatable/{report}','HomeController@searchData');
Route::get('/updatefield', 'UpdateController@updateField')->name('updateField');
Route::get('/updatesuggest', 'UpdateController@updateSuggest')->name('updateSuggest');