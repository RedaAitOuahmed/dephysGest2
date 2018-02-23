<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BonDeCommande extends Model
{

    protected $document;
    protected $documentBooted;
    function __construct($attributes = [])
    {       
        parent::__construct($attributes);         
        $this->document = new Document($attributes); 
        $this->documentBooted = false;     
        
    }

    private function documentBoot()
    {
        if(!$this->documentBooted && $this->documents()->first() != null)
        {           
            $this->document = $this->documents()->first();
            $this->documentBooted = true;
        }
    }


    public function save(array $options = [])
    {
        $result = parent::save($options);
        $this->documentBoot();
        $this->document->documentable_id = $this->id;
        $this->document->documentable_type = '\App\BonDeCommande';        
        return $this->document->save() && $result;

    }

    public function __set($key, $value)
    {
        $this->documentBoot();
        if( ! in_array($key,Schema::getColumnListing(parent::getTable())))
        {
            $this->document->$key = $value;
        }else
        {
            parent::__set($key,$value);
        }
    }

    public function __get($key)
    {
        $this->documentBoot();
        if( ! in_array($key,Schema::getColumnListing(parent::getTable())))
        {
           return $this->document->$key;
        }else
        {
            return parent::__get($key);
        }
    }


    public function documents()
    {
        return $this->morphMany('App\Document', 'documentable');
    }
    public function paiements()
    {
        return $this->morphMany('App\Document', 'payable');
    }
    
}
