<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Personne extends Model
{

    protected $fillable = ['user_id','prenom','entreprise','fonction'];
    public $timestamps = false;
    protected $contact;
    function __construct($attributes = [])
    {       
        parent::__construct($attributes);         
        $this->contact = new Contact($attributes);      
        
    }


    public function user()
    {
        // key used on this table = user_id
        // and matched with the id on the user table
        return $this->belongsTo('App\User'); 
    }
   
    
    public function contacts()
    {
        return $this->morphMany('App\Contact', 'contactable');
    }

    public function save(array $options = [])
    {
        $result = parent::save($options);
        $this->contact->contactable_id = $this->id;
        $this->contact->contactable_type = '\App\Personne';        
        return $this->contact->save() && $result;

    }
    public function dummyDisplay()
    {
      
             echo "Imma person";
    }

    public function entreprises()
    {
        // return $this->belongsToMany('App\Entreprise','entreprise_employes','id','id');
        return $this->belongsToMany('App\Personne');
    }

    public function __set($key, $value)
    {
        if( ! in_array($key,Schema::getColumnListing(parent::getTable())))
        {
            $this->contact->$key = $value;
        }else
        {
            parent::__set($key,$value);
        }
    }

    public function __get($key)
    {
        
        if( ! in_array($key,Schema::getColumnListing(parent::getTable())))
        {
           return $this->contact->$key;
        }else
        {
            return parent::__get($key);
        }
    }

}
