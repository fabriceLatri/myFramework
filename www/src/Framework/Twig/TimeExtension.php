<?php

namespace Framework\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TimeExtension extends AbstractExtension
{

    
    /**
     * getFilters
     *
     * @return TwigFilter[]
     */
    public function getFilters()
    {
        return [
            new TwigFilter('ago', [$this, 'ago'], ['is_safe' => ['html']])
        ];
    }

    public function ago(\DateTime $date, string $format = 'd/m/Y H:i')
    {
        return'<span 
        class="timeago" 
        datetime="' . $date->format(\DateTime::ISO8601) . '">'
         . $date->format($format) .
         '</span>';
    }
}
