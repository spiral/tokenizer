<?php
/**
 * Spiral, Core Components
 *
 * @author Wolfy-J
 */
namespace Spiral\Tests\Tokenizer;

use Spiral\Tokenizer\Highlighter;

class HighlighterTest extends \PHPUnit_Framework_TestCase
{
    public function testHighlightAll()
    {
        $highlighter = new Highlighter();
        $highlighter = $highlighter->withSource(file_get_contents(__FILE__));

        $this->assertInternalType('string', $highlighter->highlight());
        $this->assertSame(count(file(__FILE__)), count(explode("\n", $highlighter->highlight())));
    }

    public function testHighlightAllDarkStyle()
    {
        $highlighter = new Highlighter('', new Highlighter\InversedStyle());
        $highlighter = $highlighter->withSource(file_get_contents(__FILE__));

        $this->assertInternalType('string', $highlighter->highlight());
        $this->assertSame(count(file(__FILE__)), count(explode("\n", $highlighter->highlight())));
    }

    public function testHighlightWithDarkStyle()
    {
        $highlighter = new Highlighter();
        $highlighter = $highlighter->withSource(file_get_contents(__FILE__));

        $highlighter = $highlighter->withStyle(new Highlighter\InversedStyle());
        $this->assertInternalType('string', $highlighter->highlight());
        $this->assertSame(count(file(__FILE__)), count(explode("\n", $highlighter->highlight())));
    }

    public function testHighlightLines()
    {
        $highlighter = new Highlighter();
        $highlighter = $highlighter->withSource(file_get_contents(__FILE__));

        $this->assertInternalType('string', $highlighter->lines(11));
        $this->assertSame(5, count(explode("\n", $highlighter->lines(11, 2))));
        $this->assertContains('Spiral\Tokenizer\Highlighter', $highlighter->lines(11, 2));
    }

    public function testHighlightLinesOutOfRange()
    {
        $highlighter = new Highlighter();
        $highlighter = $highlighter->withSource(file_get_contents(__FILE__));

        $this->assertInternalType('string', $highlighter->lines(12));
        $this->assertSame(5, count(explode("\n", $highlighter->lines(12, 2))));
        $this->assertNotContains('Spiral\Tokenizer\Highlighter', $highlighter->lines(12, 2));
    }
}