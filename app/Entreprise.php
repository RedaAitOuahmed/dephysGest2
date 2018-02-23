<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Entreprise extends Model
{
    protected $fillable = ['siren','siret','assujetiTVA','numTVA'];
    protected $contact;
    protected $contactBooted;

    function __construct($attributes = [])
    {       
        parent::__construct($attributes);                   
        $this->contact = new Contact($attributes);
        $this->contactBooted = false;  
        
    }

    private function bootContact()
    {

        if(!$this->contactBooted && $this->contacts()->first() != null)
        {           
            $this->contact = $this->contacts()->first();
            $this->contactBooted = true;
        }  
    }

    public function save(array $options = [])
    {
        $result = parent::save($options);
        $this->bootContact();
        $this->contact->contactable_id = $this->id;
        $this->contact->contactable_type = '\App\Entreprise';        
        return $this->contact->save() && $result;

    }

    public function __set($key, $value)
    {
        $this->bootContact();
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
        $this->bootContact();        
        if( ! in_array($key,Schema::getColumnListing(parent::getTable())))
        {
           return $this->contact->$key;
        }else
        {
            return parent::__get($key);
        }
    }
    
    public function contacts()
    {
        return $this->morphMany('App\Contact', 'contactable');
    }

    public function employees()
    {
        return $this->belongsToMany('App\Personne');
    }
   
   
}
