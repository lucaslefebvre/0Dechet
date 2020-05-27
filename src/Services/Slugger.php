<?php

namespace App\Services;

class Slugger
{
    /**
     * This function create a slug based on one or two input strings
    * @return string new slug
    */
    public function slugify(String $stringToConvert, String $optionalString = null)
    {
        if ($optionalString == null) {
            
            $stringToConvert = strtolower($stringToConvert);
            return preg_replace( '/\W+/', '-', trim(strip_tags($stringToConvert)));
        }
        else {
            $newStringToConvert = $stringToConvert . ' ' . $optionalString;

            $newStringToConvert = strtolower($newStringToConvert);
            return preg_replace( '/\W+/', '-', trim(strip_tags($newStringToConvert)));
        }
    }
}