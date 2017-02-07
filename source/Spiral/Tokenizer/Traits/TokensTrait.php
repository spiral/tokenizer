<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Tokenizer\Traits;

use Spiral\Tokenizer\TokenizerInterface;

/**
 * Normalizes tokens by forcing them into same format.
 */
trait TokensTrait
{
    /**
     * Normalize tokens by wrapping every token into array and forcing line value.
     *
     * @param array $tokens
     *
     * @return array
     */
    private function normalizeTokens(array $tokens): array
    {
        $line = 0;
        foreach ($tokens as &$token) {
            if (isset($token[TokenizerInterface::LINE])) {
                $line = $token[TokenizerInterface::LINE];
            }

            if (!is_array($token)) {
                $token = [$token, $token, $line];
            }

            unset($token);
        }

        return $tokens;
    }
}
