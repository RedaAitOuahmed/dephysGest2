<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable
{
    use Notifiable;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'password','email'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
     /**
     *  @var Personne a Personne instance that holds all the Personne (and Contact) Info
     */

    protected $personne;
    /**
     * @var Boolean  indicates whether or not the $personne was retrieved from the database 
     */

    protected $personneBooted;
    /**
     * initiates this instance and a new Personne instance and affects it to $personne.
     */
    function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->personne = new \App\Personne($attributes);
        $this->personneBooted = false;
    }
     /**
     * tires to retrive the Personne instance that belongs to this instance from the Database.
     * sets personneBooted to true if this was done.
     */

    private function bootPersonne()
    {

        if(!$this->personneBooted && $this->personne()->first() != null)
        {           
            $this->personne = $this->personne()->first();
            $this->personneBooted = true;
        }
    
    }

     /**
     * @overrides Model::save(array $options = [])
     * 1 : use Model::save to save $this instance;
     * boots $personne
     * 2 : uses save method on $personne
     * @returns the AND of both 1 and 2 operations
     */ 
    public function save(array $options = [])
    {
        $result = parent::save($options);   
        $this->bootPersonne();  
        $this->personne()->save($this->personne);
        
    }
    /**
     * overrides Model::__set
     * sets an attribute whether it belongs to this Instance or to the contact instance.
     * sets it in both instances if it belongs to both instances.
     * if the attribute is  on this model table
     *  sets it on the this instance,
     * if the attribute is  on this personne (or contact) table
     *  sets it on the contact model.
     */

    public function __set($key, $value)
    {
        $this->bootPersonne();
        
        if(in_array($key,Schema::getColumnListing(parent::getTable())))
        {
            parent::__set($key,$value);
        }
        if ( in_array($key,$this->personne->getAllColumns()) )
        {
            $this->personne->$key = $value;
        }
    }

    /**
     * @overrides Model::__get
     * returns the attribute either from this model or from the personne (or contact) model
     */

    public function __get($key)
    {
        $this->bootPersonne();
        if( ! in_array($key,Schema::getColumnListing(parent::getTable())))
        {
           return $this->personne->$key;
        }else
        {
            return parent::__get($key);
        }
    }
     /**
     * @returns an array containing the names of all the columns
     * including this model's columns and the personne(and contact) model's columns
     */
    public function getAllColumns()
    {
        return array_merge(
            Schema::getColumnListing(parent::getTable()),
            $this->personne->getAllColumns()
        );
    }    

    public function personne()
    {
        // key used on this table = id
        // and matched with the user_id on the personne table
        return $this->hasOne('App\Personne','user_id','id');
    }
    /**
     * contacts a user added and so on
     */
    public function contacts_added()
    {
        return $this->hasMany('App\Contact','added_by');
    }

    public function projets_added()
    {
        return $this->hasMany('App\Projet','added_by');
    }
    public function taches_added()
    {
        return $this->hasMany('App\Tache','added_by');
    }
    /**
     * taches assigned to this  user.
     */
    public function taches_assigned()
    {
        return $this->belongsToMany('App\Tache','taches_users','id','id');
    }
    public function fichiers_added()
    {
        return $this->hasMany('App\Fichier','added_by');
    }

    public function produits_added()
    {
        return $this->hasMany('App\Produit','added_by');
    }
    public function categories_added()
    {
        return $this->hasMany('App\Categorie','added_by');
    }
    public function documents_added()
    {
        return $this->hasMany('App\Document','added_by');
    }
    public function paiements_added()
    {
        return $this->hasMany('App\Document','added_by');
    }
    public function factures_finales_added()
    {
        return $this->hasMany('App\FactureFinale','finalized_by');
    }


   

}
