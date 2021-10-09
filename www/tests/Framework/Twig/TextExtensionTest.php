<?php

namespace Tests\Framework\Twig;

use PHPUnit\Framework\TestCase;
use Framework\Twig\TextExtension;

class TextExtensionTest extends TestCase
{
    /**
     * textExtension
     *
     * @var TextExtension
     */
    private $textExtension;

    
    public function setUp(): void
    {
        $this->textExtension = new TextExtension();
    }

    public function testExcerptWithShortTest()
    {
        $text = "Salut";
        $this->assertEquals($text, $this->textExtension->excerpt($text, 10));
    }

    public function testExcerptWithLongTest()
    {
        $text = "Salut les gens";
        $this->assertEquals('Salut les...', $this->textExtension->excerpt($text, 12));
    }
}
