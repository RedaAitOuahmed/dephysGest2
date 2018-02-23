<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Entreprise extends Model
{
    protected $fillable = ['siren','siret','assujetiTVA','numTVA'];
    public $timestamps=false;
    protected $contact;   
    function __construct($attributes = [])
    {       
        parent::__construct($attributes); 
        if( ! empty($attributes))
        {
            $this->contact = new Contact($attributes);
        }      
        
    }
    public function save(array $options = [])
    {
        $result = parent::save($options);
        $this->contact->contactable_id = $this->id;
        $this->contact->contactable_type = '\App\Entreprise';        
        return $this->contact->save() && $result;

    }
    public function contacts()
    {
        return $this->morphMany('App\Contact', 'contactable');
    }

    public function employees()
    {
        // return $this->belongsToMany('App\Personne','entreprise_employes','id','id');
        return $this->belongsToMany('App\Personne');
    }
    public function dummyDisplay()
    {
        echo "Imma a company";
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
