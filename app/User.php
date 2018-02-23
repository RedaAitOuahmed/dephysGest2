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
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $personne;

    protected $personneBooted;

    function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->personne = new \App\Personne($attributes);
        $this->personneBooted = false;
    }


    private function bootPersonne()
    {

        if(!$this->personneBooted && $this->personne()->first() != null)
        {           
            $this->personne = $this->contacts()->first();
            $this->personneBooted = true;
        }
    
    } 
    public function save(array $options = [])
    {
        $result = parent::save($options);   
        $this->bootPersonne();     
        $this->personne()->save($this->personne);
    }

    public function __set($key, $value)
    {
        $this->bootPersonne();
        if( ! in_array($key,Schema::getColumnListing(parent::getTable())))
        {
            $this->personne->$key = $value;
        }else
        {
            parent::__set($key,$value);
        }
    }

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

    public function personne()
    {
        // key used on this table = id
        // and matched with the user_id on the personne table
        return $this->hasOne('App\Personne','user_id','id');
    }

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
