<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('contacts')->group(function () {
    //Route::get('/add', 'Contact@add')->name('addContact');
    Route::get('/filterByTypeAndRelation', 'ContactController@getContactsFiltred' );
    //->name('getContactsFiltredByTypeAndRelation');

    Route::post('/', 'ContactController@saveContact')->name('saveContact');
    Route::get('/{id}','ContactController@getContact')->name('getContact');
    Route::get('/','ContactController@getAllContacts')->name('getAllContacts');
    Route::put('/{id}','ContactController@updateContact')->name('updateContact');
    Route::delete('/{id}','ContactController@deleteContact')->name('deleteContact');
    Route::get('/workingAt/{companyContactId}','ContactController@getContactsWorkingAt')
            ->name('getContactsWorkingAt');
    
   
    
   
});

