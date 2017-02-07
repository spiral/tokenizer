<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Tests\Tokenizer\Classes;

use Spiral\Tests\Tokenizer\TestInterface;
use Spiral\Tests\Tokenizer\TestTrait;

class ClassB extends ClassA implements TestInterface
{
    use TestTrait;
}