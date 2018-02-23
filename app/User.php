<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable
{
    use Notifiable;
    protected $personneAtt;
    function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->personneAtt = new \App\Personne($attributes);
       
    }

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

    public function personne()
    {
        // key used on this table = id
        // and matched with the user_id on the personne table
        return $this->hasOne('App\Personne','user_id','id');
    }

    public function projets_created()
    {
        return $this->hasMany('App\Projet','created_by_id','id');
    }
    public function taches_created()
    {
        return $this->hasMany('App\Tache','created_by_id','id');
    }
    public function taches_assigned()
    {
        return $this->belongsToMany('App\Tache','taches_users','id','id');
    }
    public function fichiers_added()
    {
        return $this->hasMany('App\Fichier','created_by_id','id');
    }
    

    

    public function produits_added()
    {
        return $this->hasMany('App\Produit');
    }
    public function categories_added()
    {
        return $this->hasMany('App\Categorie');
    }


    public function save(array $options = [])
    {
        $result = parent::save($options);        
        $this->personne()->save($this->personneAtt);
    }

    public function __set($key, $value)
    {
        if( ! in_array($key,Schema::getColumnListing(parent::getTable())))
        {
            $this->personneAtt->$key = $value;
        }else
        {
            parent::__set($key,$value);
        }
    }

    public function __get($key)
    {
        
        if( ! in_array($key,Schema::getColumnListing(parent::getTable())))
        {
           return $this->personneAtt->$key;
        }else
        {
            return parent::__get($key);
        }
    }

}
