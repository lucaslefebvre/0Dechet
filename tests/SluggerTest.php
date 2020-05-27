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
        $string = 'Le savon de M@rseille';

        $result = $slugger->slugify($string);
        dump($result);
        $this->assertEquals('le-savon-de-m-rseille', $result);

        //Test with two strings//
        $slugger = new Slugger;
        $string = 'Le savon de Marseille';
        $optionalString = '2';

        $result = $slugger->slugify($string, $optionalString);
        dump($result);
        $this->assertEquals('le-savon-de-marseille-2', $result);
    }
}
