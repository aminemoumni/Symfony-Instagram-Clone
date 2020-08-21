<?php

namespace App\Twig;

use Twig\TwigFilter;
use Twig\TwigTest;
use App\Entity\LikeNotification;
use Twig\Extension\GlobalsInterface;
use Twig\Extension\AbstractExtension;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    private $locale;
    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }
    public function getFilters()
    {
        return [
            new TwigFilter('price', [$this, 'formatPrice']),
            
        ];
    }
    public function getGlobals():array
    {
        return [
            'locale' => $this->locale
        ];
    }

    public function formatPrice($number, $decimals = 2, $decPoint = '.', $thousandsSep = ',')
    {
        $price = number_format($number, $decimals, $decPoint, $thousandsSep);
        $price = '$'.$price;

        return $price;
    }

    public function getTests()
    {
        return [
            new TwigTest(
                'like', 
                function($obj) { return $obj instanceof LikeNotification;})
        ];
    }
     
}