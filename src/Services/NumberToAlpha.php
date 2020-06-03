<?php

namespace App\Services;

class NumberToAlpha
{
    /**
    * This function given a number converts it into a letters string
    * @return string of letter
     */
    public function toAlpha($number)
    {
        $alphabet =array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');

        if($number <= 25){
          return $alphabet[$number];
        }
        elseif($number > 25){
          $dividend = ($number + 1);
          $alpha = '';
          while ($dividend > 0){
            $modulo = ($dividend - 1) % 26;
            $alpha = $alphabet[$modulo] . $alpha;
            $dividend = floor((($dividend - $modulo) / 26));
          } 
          return $alpha;
        }

    }
} 