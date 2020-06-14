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
            $search = explode(",",
            "@,ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,ø,Ø,Å,Á,À,Â,Ä,È,É,Ê,Ë,Í,Î,Ï,Ì,Ò,Ó,Ô,Ö,Ú,Ù,Û,Ü,Ÿ,Ç,Æ,Œ");
            $replace = explode(",",
            "a,c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,o,O,A,A,A,A,A,E,E,E,E,I,I,I,I,O,O,O,O,U,U,U,U,Y,C,AE,OE");
            $string = str_replace($search, $replace, $stringToConvert);
            $str = strtolower($string);
            return preg_replace( '/\W+/', '-', trim(strip_tags($str)));
        }
        else {
            $newStringToConvert = $stringToConvert . ' ' . $optionalString;
            $search = explode(",",
            "@,ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,ø,Ø,Å,Á,À,Â,Ä,È,É,Ê,Ë,Í,Î,Ï,Ì,Ò,Ó,Ô,Ö,Ú,Ù,Û,Ü,Ÿ,Ç,Æ,Œ");
                $replace = explode(",",
            "a,c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,o,O,A,A,A,A,A,E,E,E,E,I,I,I,I,O,O,O,O,U,U,U,U,Y,C,AE,OE");
            $string = str_replace($search, $replace, $newStringToConvert);
            $str = strtolower($string);
            return preg_replace( '/\W+/', '-', trim(strip_tags($str)));
        }
    }
}