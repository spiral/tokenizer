<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Tokenizer\Tests\Classes;

use Spiral\Tokenizer\Tests\Fixtures\TestInterface;
use Spiral\Tokenizer\Tests\Fixtures\TestTrait;

class ClassB extends ClassA implements TestInterface
{
    use TestTrait;
}
