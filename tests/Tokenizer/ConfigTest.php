<?php
/**
 * Spiral, Core Components
 *
 * @author Wolfy-J
 */

namespace Spiral\Tests\Tokenizer;

use Spiral\Tokenizer\Configs\TokenizerConfig;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testDirectories()
    {
        $config = new TokenizerConfig([
            'directories' => ['a', 'b', 'c']
        ]);
        $this->assertSame(['a', 'b', 'c'], $config->getDirectories());
    }

    public function testExcluded()
    {
        $config = new TokenizerConfig([
            'exclude' => ['a', 'b', 'c']
        ]);
        $this->assertSame(['a', 'b', 'c'], $config->getExcludes());
    }
}