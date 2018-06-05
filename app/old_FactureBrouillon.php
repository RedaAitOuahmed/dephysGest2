<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FactureBrouillon extends Model
{
 /**
     *  $document is a Document instance that holds all the Document Info
     */
    protected $document;
    /**
     * Boolean that indicates whether or not the $document was retrieved from the database 
     */
    protected $documentBooted;
    /**
     * initiates a new Document instance and affects it to document.
     */
    function __construct($attributes = [])
    {       
        parent::__construct($attributes);         
        $this->document = new Document($attributes); 
        $this->documentBooted = false;     
        
    }
    /**
     * tires to retrive the Document instance that belongs to this instance from the Database.
     * sets documentBooted to true if this was done.
     */

    private function documentBoot()
    {
        if(!$this->documentBooted && $this->documents()->first() != null)
        {           
            $this->document = $this->documents()->first();
            $this->documentBooted = true;
        }
    }

    /**
     * @overrides Model::save(array $options = [])
     * 1 : use Model::save to save $this instance;
     * boots the document
     * 2 : uses save method on $document
     * @returns the AND of both 1 and 2 operations
     */



    public function save(array $options = [])
    {
        $result = parent::save($options);
        $this->documentBoot();
        $this->document->documentable_id = $this->id;
        $this->document->documentable_type = '\App\BonDeCommande';        
        return $this->document->save() && $result;

    }

       /**
     * overrides Model::__set
     * sets an attribute wether it belongs to this Instance or to the document instance.
     * if the attribute is not on this model table
     *  sets it on the document instance,
     * else
     *  sets it on this model.
     */

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
        /**
     * @overrides Model::__get
     * returns the attribute either from this model or from the document model
     */


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

    /**
     * defines the relation to documents table
     */
    
    public function documents()
    {
        return $this->morphMany('App\Document', 'documentable');
    }
}
