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
  
          // And now, let's create a few articles in our database:
          for ($i = 0; $i < 50; $i++) {
              App\Contact::create([
                  'nom' => $faker->name,
                  'relation' => $faker->randomElement($array = 
                        array ('client','fournisseur','collegue','prospect')),
                    'email' => $faker->safeEmail,
                    'tel' => $faker->phoneNumber,
                    'adresse' => $faker->address,
                    'addedBy' => 0 
              ]);
          }
    }
}
