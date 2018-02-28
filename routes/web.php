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
   
    // $contact1 = new App\User(['nom'=>'reda',
    //         'relation' => 'client',
    //         'tel' => '07751264',
    //         'adresse' => '100 impasse de toto',
    //         'fax' => '012354698',
    //         'contactable_id' =>'1',
    //         'contactable_type'=>'App\Personne',
    //         'prenom'=>'moh',
    //         'added_by' => '0'
    //         ]);
    // $contact1->email = 'aaaa@bbbgb.com';
    // $contact1->save();

    // $contact1 = new App\User([
    //     'nom'=>'reda',
    //     'relation' => 'client',
    //     'email'=>'aaaa@bbbb.com',
    //     'tel' => '07751264',
    //     'adresse' => '100 impasse de toto',
    //     'fax' => '012354698',
    //     'contactable_id' =>'1',
    //     'contactable_type'=>'App\Personne',
    //     'prenom'=>'moh',
    //     'added_by' => '0'
    //     ]);
    // // $contact1->prenom = 'a Name';
    //  $contact1->save();
    

        // $test = App\User::first();
        // $test->email = "new mailaa2aaa";
        // $test->nom ="blabla";
        // $test->prenom = "shit";
        // $test->save();


//     $pers = new App\Personne(['nom'=>'dephystech','email'=>'depdshystech@dephystech.com','prenom'=>'2121']);  
//     // $entr = new App\Entreprise(['nom'=>'dephystech','email'=>'dephystech@dephystech.com','siren'=>'2121']);
    
//     // $entr->save();

//     // $entr->nom = 'newName';
//     // $pers->nom ='reda';

//     // $entr->save();
    
//     $pers->save();
//     Debugbar::info($pers);
    
//     $test = App\Personne::first();
//     Debugbar::info($test);
    
    
//     $test->tel = '1212';
//     $test->save();


//     // $usr = new App\User(['nom'=>'dephystech','email'=>'dephystech@ds','password'=>'bla','prenom' => 'haha']);
//     // $usr->save();

//     // $usr2 = App\Personne::find(2)->user()->first();
//      Debugbar::info("fdf");
//     // $usr2 = $usr2->personne();
    
//     //echo $usr2->id;
//     // echo $usr2->nom;
//     //var_dump($usr2);
//    //var_dump($usr2->personne());
//     // var_dump($usr2->personne);
    
//     //dd(2);

// //    $pers->save();
// //     dd($pers->id);
//     // var_dump($pers->get(['prenom']));
// //    $contact1 = App\Contact::find(1);
//     // $pers->save();
//     // $contact1->save();

//     // $ap = $contact1->contactable->dummyDisplay();
  
   
//     // //var_dump($contact1);
//     // //var_dump($pers);
//     // //$a = new App\Personne('bla');
//     // // $cont->save();
    
 return view('welcome');
});
Route::get('/adminLogin', 'Auth\AdminLoginController@loginForm')->name('adminLogin');
Route::post('/adminLoginSubmit', 'Auth\AdminLoginController@login')->name('adminLoginSubmit');

Route::get('/adminHome', function()
{
    return view('adminHome');
})->name('adminHome');



Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
