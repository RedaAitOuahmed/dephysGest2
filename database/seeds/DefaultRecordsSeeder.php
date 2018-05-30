<?php

use Illuminate\Database\Seeder;

class DefaultRecordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //default Fournisseur
        $fournisseur = new \App\Contact();
        $fournisseur->nom = "fournisseur par défaut";
        $fournisseur->save();
        $rel = new \App\Relation();
        $rel->relation = "fournisseur";
        $fournisseur->relations()->save($rel);

        //default user
        $user = new \App\User();
        $user->email = 'dephysGest@dephystech.com';
        $user->nom = 'DephysGest';
        $user->password = bcrypt('secret');
        $user->save();

        //default Projet
        $projet = new \App\Projet();
        $projet->nom = 'Libre';
        $projet->description = "Ce Projet contient toutes les tâches qui n'appartienent à aucun autre projet";
        $projet->addedBy = null;
        $projet->save();

        //default Categorie
        $categorie = new \App\Categorie();
        $categorie->nom = 'Libre';
        $categorie->description = "Cette Catégorie contient tous les produits qui n'appartienent à aucune autre catégorie";
        $categorie->addedBy = null;
        $categorie->save();
                
                
    }
}
