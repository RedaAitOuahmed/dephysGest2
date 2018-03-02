<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Personne extends Model
{

    protected $fillable = ['user_id','prenom','entreprise','fonction'];
   /**
     *  @var Contact a Contact instance that holds all the Contact Info
     */
    protected $contact;
    /**
     * @var Boolean that indicates whether or not the $contact was retrieved from the database 
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
        $this->contact->contactable_type = 'App\Personne';        
        return $this->contact->save() && $result;

    }
    /**
     * overrides Model::__set
     * sets an attribute whether it belongs to this Instance or to the contact instance.
     * sets it in both instances if it belongs to both instances.
     * if the attribute is  on this model table
     *  sets it on the this instance,
     * if the attribute is  on this contact table
     *  sets it on the contact model.
     */



    public function __set($key, $value)
    {
        $this->bootContact();
        if( in_array($key,Schema::getColumnListing(parent::getTable())))
        {
            parent::__set($key,$value);
           
        }
        if( in_array($key,$this->contact->getAllColumns()) )
        {
            $this->contact->$key = $value;
        }
    }

     /**
     * @overrides Model::__get
     * returns the attribute either from this model or from the contact model
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
    /**
     * @returns an array containing the names of all the columns
     * including this model's columns and the contact model's columns
     */

    public function getAllColumns()
    {
        return array_merge(
            Schema::getColumnListing(parent::getTable()),
            $this->contact->getAllColumns()
        );
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

    

    public function entreprises()
    {
        return $this->belongsToMany('App\Personne');
    }



    /**
     * functions that belong to Contact
     */
    public function produits()
    {
        $this->bootContact();  
        return $this->contact->produits();
    }

    public function addedBy()
    {
        $this->bootContact();  
        return $this->contact->addedBy();
        
    }

   

}
