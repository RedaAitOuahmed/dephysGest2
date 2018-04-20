<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Entreprise extends Model
{
    protected $fillable = ['siren','siret','assujetiTVA','numTVA'];
    /**
     *  $contact is a Contact instance that holds all the Contact Info
     */
    protected $contact;
    /**
     * Boolean that indicates whether or not the $contact was retrieved from the database 
     */
    protected $contactBooted;

    /**
     * initiates a new Contact instance and affects it to $contact.
     */

    function __construct($attributes = [])
    {       
        parent::__construct($attributes);                   
        $this->contact = new Contact($attributes);
        $this->contactBooted = false;    
    }
    /**
     * tires to retrive the Contact instance that belongs to this instance from the Database.
     * sets contactBooted to true if this was done.
     */
    private function bootContact()
    {

        if(!$this->contactBooted && $this->contacts()->first() != null)
        {           
            $this->contact = $this->contacts()->first();
            $this->contactBooted = true;
        }  
    }

    /**
     * @overrides Model::save(array $options = [])
     * 1 : use Model::save to save $this instance;
     * boots the contact
     * 2 : uses save method on $contact
     * @returns the AND of both 1 and 2 operations
     */


    public function save(array $options = [])
    {
        $result = parent::save($options);
        $this->bootContact();
        $this->contact->contactable_id = $this->id;
        $this->contact->contactable_type = 'App\Entreprise';        
        return $this->contact->save() && $result;

    }

       /**
     * overrides Model::__set
     * sets an attribute whether it belongs to this Instance or to the document instance.
     * 
     * if the attribute is not  on this model table
     *  sets it on the contact model.
     * else
     * sets it on the this instance,
     */


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

     /**
     * @overrides Model::__get
     * returns the attribute either from this model or from the document model
     */

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
    
    public function produits()
    {
        $this->bootContact();  
        return $this->contact->produits();
    }

    public function relations()
    {
        $this->bootContact();  
        return $this->contact->relations();
    }

    public function addedBy()
    {
        $this->bootContact();  
        return $this->contact->addedBy();
    }

    public function delete()
    {       
        $this->bootContact(); 
        parent::delete();
        if($this->contact != null)
        {
            $this->contact->delete();
        }                   
    }

   
   
}
