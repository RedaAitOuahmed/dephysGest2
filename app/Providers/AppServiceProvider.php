<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        /*
        This validator is to validate that a field contained in an object inside an array is greater or equal to another field contained in the same object
        */
        \Validator::extend('greater_than_or_equal_field', function($attribute, $value, $parameters, $validator) {
            
            $data = $validator->getData();           
            $field =$data['entries'] ;
            // to get a dot notation to the parameter field of the validator, AKA the value that the attribute should be compared to. 
            $keys= \array_keys($data['entries']);
            $min_field = array_map(function ($field) use ($keys)
                                    {
                                        return vsprintf(str_replace('*', '%s', $field), $keys);
                                    }, $parameters)[0];       
           
            //retrieving the value of the parameter field
            $min_value = array_get($data, $min_field);
            
            if($min_value == null)
            {
                return true;
            }
            return $value >= $min_value;
        });   
      
        \Validator::replacer('greater_than_or_equal_field', function($message, $attribute, $rule, $parameters) {
            $message = ":attribute doit ếtre supérieur ou égale à :field ";
            $message = str_replace(':attribute', $attribute, $message);
            return str_replace(':field', $parameters[0], $message);
        });
        
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
