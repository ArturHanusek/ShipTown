<?php

/*
|--------------------------------------------------------------------------
| User Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;

Route::view('setting-profile', 'setting-profile')->name('setting-profile');

Route::redirect('', 'dashboard');
Route::view('dashboard', 'dashboard')->name('dashboard');
Route::view('performance/dashboard', 'performance')->name('performance.dashboard');
Route::view('products', 'products')->name('products');
Route::view('picklist', 'picklist')->name('picklist');
Route::view('orders', 'orders')->name('orders');

Route::view('autopilot/packlist', 'autopilot/packlist')->name('autopilot.packlist');

Route::resource('order/packsheet', 'Order\PacksheetController')->only(['show']);

Route::view('reports/picks', 'reports/picks_report')->name('reports.picks');
Route::get('reports/shipments', 'Reports\ShipmentController@index')->name('reports.shipments');

Route::get('pdf/orders/{order_number}/{template}', 'PdfOrderController@show');
Route::get('orders/{order_number}/kick', 'OrderKickController@index');
Route::get('products/24h/kick', 'Products24hKickController@index');
Route::get('products/{sku}/kick', 'ProductKickController@index');
Route::get('csv/ready_order_shipments', 'Csv\ReadyOrderShipmentController@index')->name('ready_order_shipments_as_csv');
Route::get('csv/order_shipments', 'Csv\PartialOrderShipmentController@index')->name('partial_order_shipments_as_csv');
Route::get('csv/products/picked', 'Csv\ProductsPickedInWarehouse@index')->name('warehouse_picks.csv');
Route::get('csv/products/shipped', 'Csv\ProductsShippedFromWarehouseController@index')->name('warehouse_shipped.csv');
Route::get('csv/boxtop/stock', 'Csv\BoxTopStockController@index')->name('boxtop-warehouse-stock.csv');
