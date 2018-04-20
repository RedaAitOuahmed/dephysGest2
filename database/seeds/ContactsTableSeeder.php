<?php

use Illuminate\Database\Seeder;

class ContactsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $faker = \Faker\Factory::create();

      // And now, let's create a few contacts in our database:
      for ($i = 0; $i < 10; $i++) {

        $contact = new App\Contact();
        $contact->nom = $faker->name;
        $contact->email = $faker->safeEmail;
        $contact->tel = $faker->phoneNumber;
        $contact->adresse = $faker->address;
        $contact->addedBy = 0;
        $contact->save();
        $rel = new App\Relation();
        $rel->relation = $faker->randomElement($array = 
        array ('client','fournisseur','collegue','prospect'));
        $contact->relations()->save($rel);

        $rel = new App\Relation();
        $rel->relation = $faker->randomElement($array = 
        array ('client','fournisseur','collegue','prospect'));
        $contact->relations()->save($rel);
      }

      for ($i = 0; $i < 10; $i++) {

        $contact = new App\Personne();
        $contact->nom = $faker->name;
        $contact->prenom = $faker->name;
        $contact->email = $faker->safeEmail;
        $contact->tel = $faker->phoneNumber;
        $contact->adresse = $faker->address;
        $contact->addedBy = 0;
        $contact->save();
        $rel = new App\Relation();
        $rel->relation = $faker->randomElement($array = 
        array ('client','fournisseur','collegue','prospect'));
        $contact->relations()->save($rel);

        $rel = new App\Relation();
        $rel->relation = $faker->randomElement($array = 
        array ('client','fournisseur','collegue','prospect'));
        $contact->relations()->save($rel);
      }

      for ($i = 0; $i < 10; $i++) {

        $contact = new App\Entreprise();
        $contact->nom = $faker->name;
        $contact->siret = $faker->phoneNumber;
        $contact->email = $faker->safeEmail;
        $contact->tel = $faker->phoneNumber;
        $contact->adresse = $faker->address;
        $contact->addedBy = 0;
        $contact->save();
        $rel = new App\Relation();
        $rel->relation = $faker->randomElement($array = 
        array ('client','fournisseur','collegue','prospect'));
        $contact->relations()->save($rel);

        $rel = new App\Relation();
        $rel->relation = $faker->randomElement($array = 
        array ('client','fournisseur','collegue','prospect'));
        $contact->relations()->save($rel);
      }


    }
}
