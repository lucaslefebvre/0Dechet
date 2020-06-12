<?php

namespace App\Tests;

use App\Services\Slugger;
use PHPUnit\Framework\TestCase;

class SluggerTest extends TestCase
{
    public function testSlugify()
    {
        //Test with one string//
        $slugger = new Slugger;
        $string = 'Le savon de Marseille';

        $result = $slugger->slugify($string);
        dump($result);
        $this->assertEquals('le-savon-de-marseille', $result);

        //Test with two strings//
        $slugger = new Slugger;
        $string = 'Le s@von de Méàûïrseille';
        $optionalString = '2';

        $result = $slugger->slugify($string, $optionalString);
        dump($result);
        $this->assertEquals('le-savon-de-meauirseille-2', $result);
    }
}
