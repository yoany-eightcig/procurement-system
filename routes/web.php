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
