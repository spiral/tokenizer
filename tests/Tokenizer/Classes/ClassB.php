<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Tokenizer\Tests\Classes;

use Spiral\Tokenizer\Tests\TestInterface;
use Spiral\Tokenizer\Tests\TestTrait;

class ClassB extends ClassA implements TestInterface
{
    use TestTrait;
}