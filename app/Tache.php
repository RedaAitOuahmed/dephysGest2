<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tache extends Model
{
    protected $fillable = [
        'nom','description','visibleAuxAutres','dateLimite','etat','projet_id',
    ];
    public function projet()
    {
        return $this->belongsTo('App\Projet');
    }

    public function fichiers()
    {
        return $this->hasMany('App\Fichier');
    }

    public function addedBy()
    {
       return $this->belongsTo('App\User','addedBy'); 
    }

    public function assigned_to()
    {
       return $this->belongsToMany('App\User','taches_users','tache_id','user_id'); 
    }

    /**
     * @param userId a user id 
     * @return  bollean to indicate wether this task is visible to userId
     */
    public function isVisibleTo($userId)
    {
        return $this->visibleAuxAutres || $this->addedBy == $userId;
    }
     /**
     * @param userId a user id 
     * @return  bollean to indicate wether this task can be edited by userId
     */
    public function editableBy($userId)
    {
        if($this->addedBy == $userId || User::find($userId)->superUser)
        {
            return true;
        }
        $userIds = $this->assigned_to()->distinct()->pluck('user_id')->toArray();
        if(\in_array($userId,$userIds))
        {
            return true;
        }
        return false;        
    }

    /**
     * @return array of user ids that this task is assigned to
     */
    public function getAssignations()
    {
        $userIds = $this->assigned_to()->distinct()->pluck('user_id')->toArray();
        foreach ($userIds as $key => $value)
        {
            // finds user instance
            $userIds[$key] = \App\User::find($value)->getContactId();
        }
        return $userIds;
    }
    /**
     * finds the user ids that match the contact ids passed in the in the contactIds array
     * set the assignations of this task to all user ids  
     */
    public function setAssignations($contactIds)
    {
        foreach ($contactIds as $key => $value)
        {
            //finds contact instance
            $contactIds[$key] = \App\Contact::find($value)->getUserId();
        }
        $this->assigned_to()->sync($contactIds);
    }
    /**
     * deletes this model from database
     * and all assignations of this tache
     */
    public function delete()
    {
        $this->assigned_to()->detach();
        return parent::delete();
    }
    
}
