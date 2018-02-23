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
   
    //$contact1 = new App\Contact(['nom'=>'reda', 'contactable_id' =>'1', 'contactable_type'=>'App\Personne','prenom'=>'moh']);
    // $contact1->save();
    $pers = new App\Personne(['nom'=>'dephystech','email'=>'depdshystech@dephystech.com','prenom'=>'2121']);  
    // $entr = new App\Entreprise(['nom'=>'dephystech','email'=>'dephystech@dephystech.com','siren'=>'2121']);
    
    // $entr->save();

    // $entr->nom = 'newName';
    // $pers->nom ='reda';

    // $entr->save();
    
    $pers->save();
    Debugbar::info($pers);
    
    $test = App\Personne::first();
    Debugbar::info($test);
    
    
    $test->tel = '1212';
    $test->save();


    // $usr = new App\User(['nom'=>'dephystech','email'=>'dephystech@ds','password'=>'bla','prenom' => 'haha']);
    // $usr->save();

    // $usr2 = App\Personne::find(2)->user()->first();
     Debugbar::info("fdf");
    // $usr2 = $usr2->personne();
    
    //echo $usr2->id;
    // echo $usr2->nom;
    //var_dump($usr2);
   //var_dump($usr2->personne());
    // var_dump($usr2->personne);
    
    //dd(2);

//    $pers->save();
//     dd($pers->id);
    // var_dump($pers->get(['prenom']));
//    $contact1 = App\Contact::find(1);
    // $pers->save();
    // $contact1->save();

    // $ap = $contact1->contactable->dummyDisplay();
  
   
    // //var_dump($contact1);
    // //var_dump($pers);
    // //$a = new App\Personne('bla');
    // // $cont->save();
    
    return view('welcome');
});
