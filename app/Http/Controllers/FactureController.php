<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Facture;
use \App\DocumentEntry;
use Auth;
use \App\Http\Resources\FactureResouce;


class FactureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {       
        $this->middleware('auth:api');
        
    }

    public function getAll()
    {
        return FactureResource::collection(Facture::paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        //TODO VALIDATION
        $fact = new Facture($request->all());
        $fact->addedBy = Auth::user()->id;
        $fact->save();
        foreach($request->entries as $entry)
        {
            $line = new DocumentEntry($entry);
            $line->addedBy = Auth::user()->id;
            $fact->document_entries()->save($line);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($id)
    {
        $fact = Facture::find($id);
        if($fact)
        {
            return new FactureResource($fact);
        }
        return response()->json(['message'=>'Facture not found'],404);
        
    }


    /**
     * toggle abondonnee from true to false.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleAbandonnee($id)
    {
        $fact = Facture::find($id);
        if($fact)
        {
            $fact->abandonnee = ! $fact->abandonnee;
            return response()->json(['message'=>'Facture updated'],200);
        }
        return response()->json(['message'=>'Facture not found'],404);
    }

}
