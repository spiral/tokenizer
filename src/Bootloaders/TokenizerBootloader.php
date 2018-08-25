<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Tokenizer\Bootloaders;

use Spiral\Core\Bootloaders\Bootloader;
use Spiral\Tokenizer\ClassesInterface;
use Spiral\Tokenizer\ClassLocator;
use Spiral\Tokenizer\InvocationsInterface;
use Spiral\Tokenizer\InvocationsLocator;
use Spiral\Tokenizer\Tokenizer;
use Spiral\Tokenizer\TokenizerInterface;

class TokenizerBootloader extends Bootloader
{
    const BINDINGS = [
        TokenizerInterface::class   => Tokenizer::class,
        ClassesInterface::class     => ClassLocator::class,
        InvocationsInterface::class => InvocationsLocator::class
    ];
}