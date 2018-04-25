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
    Route::get('/filterByTypeAndRelation', 'ContactController@getContactsFiltred' )
        ->name('getContactsFiltredByTypeAndRelation');
    Route::post('/', 'ContactController@saveContact')->name('saveContact');
    Route::get('/{id}','ContactController@getContact')->name('getContact');
    Route::get('/','ContactController@getAllContacts')->name('getAllContacts');
    Route::put('/{id}','ContactController@updateContact')->name('updateContact');
    Route::delete('/{id}','ContactController@deleteContact')->name('deleteContact');
    Route::get('/workingAt/{companyContactId}','ContactController@getContactsWorkingAt')
            ->name('getContactsWorkingAt'); 
});

Route::prefix('taches')->group(function () {
    Route::post('filterByProjetAndVisibilityAndAssignation','TacheController@getTachesFiltred')->name('getTachesFiltred');
    Route::get('projet/{projetId}','TacheController@getTachesOfAProjet')->name('getTachesOfAProjet');
    Route::delete('projet/{projetId}','TacheController@deleteTachesOfAProjet')->name('deleteTachesOfAProjet');
    Route::post('/', 'TacheController@saveTache')->name('saveTache');
    Route::get('/', 'TacheController@getAllTaches')->name('getAllTaches');
    Route::put('/{tacheId}','TacheController@updateTache')->name('updateTache');
    Route::get('/{tacheId}','TacheController@getTache')->name('getTache');
    Route::delete('/{tacheId}','TacheController@deleteTache')->name('deleteTache');
   
});

