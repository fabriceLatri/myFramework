<?php

namespace Framework\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * TextExtension
 *
 * @package Framework\Twig
 */
class TextExtension extends AbstractExtension
{
    /**
     * getFilters
     *
     * @return \TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('excerpt', [$this, 'excerpt'])
        ];
    }
    
    /**
     * Renvoie un extrait du contenu
     *
     * @param  mixed $content
     * @param  mixed $maxLength
     * @return string
     */
    public function excerpt(string $content, int $maxLength = 100): string
    {
        if (mb_strlen($content) > $maxLength) {
            $excerpt = mb_substr($content, 0, $maxLength);
            $lastSpace = mb_strrpos($excerpt, ' ');
            return mb_substr($excerpt, 0, $lastSpace) . '...';
        }
        return $content;
    }
}
