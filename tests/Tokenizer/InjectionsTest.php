<?php
/**
 * Spiral, Core Components
 *
 * @author Wolfy-J
 */

namespace Spiral\Tokenizer\Tests;

use PHPUnit\Framework\TestCase;
use Spiral\Core\BootloadManager;
use Spiral\Core\Container;
use Spiral\Tokenizer\Bootloader\TokenizerBootloader;
use Spiral\Tokenizer\ClassesInterface;
use Spiral\Tokenizer\ClassLocator;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\InvocationsInterface;
use Spiral\Tokenizer\InvocationsLocator;

class InjectionsTest extends TestCase
{
    public function testClassLocator()
    {
        $container = new Container();
        $container->bind(TokenizerConfig::class, new TokenizerConfig([
            'directories' => [__DIR__],
            'exclude'     => []
        ]));
        $bootloader = new BootloadManager($container);
        $bootloader->bootload([TokenizerBootloader::class]);

        $this->assertInstanceOf(
            ClassLocator::class,
            $container->get(ClassesInterface::class)
        );
    }

    public function testInvocationsLocator()
    {
        $container = new Container();
        $container->bind(TokenizerConfig::class, new TokenizerConfig([
            'directories' => [__DIR__],
            'exclude'     => []
        ]));

        $bootloader = new BootloadManager($container);
        $bootloader->bootload([TokenizerBootloader::class]);

        $this->assertInstanceOf(
            InvocationsLocator::class,
            $container->get(InvocationsInterface::class)
        );
    }
}