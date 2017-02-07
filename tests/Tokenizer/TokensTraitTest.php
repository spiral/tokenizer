<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Tests\Tokenizer;

use Spiral\Tokenizer\TokenizerInterface;
use Spiral\Tokenizer\Traits\TokensTrait;

class TokensTraitTest extends \PHPUnit_Framework_TestCase
{
    use TokensTrait;

    public function testNormalization()
    {
        /*
         * Some tokens will be just a stings.
         */
        $tokens = token_get_all(file_get_contents(__FILE__));
        $normalized = $this->normalizeTokens($tokens);

        $this->assertSame(count($tokens), count($normalized));

        foreach ($normalized as $index => $token) {
            $this->assertInternalType('array', $token);

            if (is_string($tokens[$index])) {
                $this->assertSame($tokens[$index], $token[TokenizerInterface::CODE]);
            } else {
                $this->assertSame($tokens[$index][0], $token[TokenizerInterface::TYPE]);
                $this->assertSame($tokens[$index][1], $token[TokenizerInterface::CODE]);
                $this->assertSame($tokens[$index][2], $token[TokenizerInterface::LINE]);
            }
        }
    }
}