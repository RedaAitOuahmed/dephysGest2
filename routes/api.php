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
    Route::post('/', 'ContactController@save')->name('saveContact');
    Route::get('/{id}','ContactController@get')->name('getContact');
    Route::get('/','ContactController@getAll')->name('getAllContacts');
    Route::put('/{id}','ContactController@update')->name('updateContact');
    Route::delete('/{id}','ContactController@delete')->name('deleteContact');
    Route::get('/workingAt/{companyContactId}','ContactController@getContactsWorkingAt')
            ->name('getContactsWorkingAt'); 
});

Route::prefix('taches')->group(function () {
    Route::post('filterByProjetAndVisibilityAndAssignation','TacheController@getTachesFiltred')->name('getTachesFiltred');
    Route::get('projet/{projetId}','TacheController@getTachesOfAProjet')->name('getTachesOfAProjet');
    Route::delete('projet/{projetId}','TacheController@deleteTachesOfAProjet')->name('deleteTachesOfAProjet');
    Route::post('/', 'TacheController@save')->name('saveTache');
    Route::get('/', 'TacheController@getAll')->name('getAllTaches');
    Route::put('/{tacheId}','TacheController@update')->name('updateTache');
    Route::get('/{tacheId}','TacheController@get')->name('getTache');
    Route::delete('/{tacheId}','TacheController@delete')->name('deleteTache');
   
});

Route::prefix('projets')->group(function () {
    Route::post('/', 'ProjetController@save')->name('saveProjet');
    Route::get('/', 'ProjetController@getAll')->name('getAllProjets');
    Route::put('/{id}','ProjetController@update')->name('updateProjet');
    Route::get('/{id}','ProjetController@get')->name('getProjet');
    Route::delete('/{id}','ProjetController@delete')->name('deleteProjet');
   
});

Route::prefix('fichiers')->group(function () {
    Route::post('/search', 'FichierController@search')->name('searchFichier');
    Route::get('/download/{id}', 'FichierController@download')->name('downloadFichier');
    Route::get('/tache/{tacheId}', 'FichierController@getAllOfATache')->name('getAllFichiersOfATache');
    Route::post('/', 'FichierController@save')->name('saveFichier');
    Route::get('/', 'FichierController@getAll')->name('getAllFichiers');
    Route::post('/update/{id}','FichierController@update')->name('updateFichier');
    Route::get('/{id}','FichierController@get')->name('getFichier');
    Route::delete('/{id}','FichierController@delete')->name('deleteFichier');
   
});

Route::prefix('produits')->group(function () {
    Route::post('/filter', 'ProduitController@getProduitsFiltred')->name('filterProduits');
    Route::post('/', 'ProduitController@save')->name('saveProduit');
    Route::get('/', 'ProduitController@getAll')->name('getAllProduit');
    Route::put('/{id}','ProduitController@update')->name('updateProduit');
    Route::get('/{id}','ProduitController@get')->name('getProduit');
    Route::delete('/{id}','ProduitController@delete')->name('deleteProduit');
    });


    Route::prefix('categories')->group(function () {
        Route::post('/', 'CategorieController@save')->name('saveCategorie');
        Route::get('/', 'CategorieController@getAll')->name('getAllCategories');
        Route::put('/{id}','CategorieController@update')->name('updateCategorie');
        Route::get('/{id}','CategorieController@get')->name('getCategorie');
        Route::delete('/{id}','CategorieController@delete')->name('deleteCategorie');
       
    });

