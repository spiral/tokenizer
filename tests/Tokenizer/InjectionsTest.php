<?php
/**
 * Spiral, Core Components
 *
 * @author Wolfy-J
 */

namespace Spiral\Tests\Tokenizer;

use Spiral\Core\Container;
use Spiral\Tokenizer\ClassesInterface;
use Spiral\Tokenizer\ClassLocator;
use Spiral\Tokenizer\Configs\TokenizerConfig;
use Spiral\Tokenizer\InvocationsInterface;
use Spiral\Tokenizer\InvocationsLocator;
use Spiral\Tokenizer\Tokenizer;

class InjectionsTest extends \PHPUnit_Framework_TestCase
{
    public function testClassLocator()
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__],
            'exclude'     => []
        ]));

        $container = new Container();
        $container->bind(Tokenizer::class, $tokenizer);
        $container->bind(ClassesInterface::class, ClassLocator::class);

        $this->assertInstanceOf(
            ClassLocator::class,
            $locator = $container->get(ClassesInterface::class)
        );

        $this->assertEquals($locator, $tokenizer->classLocator());
    }

    public function testInvocationsLocator()
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__],
            'exclude'     => []
        ]));

        $container = new Container();
        $container->bind(Tokenizer::class, $tokenizer);
        $container->bind(InvocationsInterface::class, InvocationsLocator::class);

        $this->assertInstanceOf(
            InvocationsLocator::class,
            $locator = $container->get(InvocationsInterface::class)
        );

        $this->assertEquals($locator, $tokenizer->invocationLocator());
    }
}