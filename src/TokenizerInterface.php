<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Tokenizer;

use Spiral\Tokenizer\Exception\ReflectionException;
use Spiral\Tokenizer\Exception\TokenizerException;
use Spiral\Tokenizer\Reflection\ReflectionFile;

/**
 * Provides ability to get file reflections and fetch normalized tokens for a specified filename.
 */
interface TokenizerInterface
{
    /**
     * Token array constants.
     */
    const TYPE = 0;
    const CODE = 1;
    const LINE = 2;

    /**
     * Get file reflection for given filename.
     *
     * @param string $filename
     *
     * @return ReflectionFile
     *
     * @throws TokenizerException
     * @throws ReflectionException
     */
    public function fileReflection(string $filename): ReflectionFile;
}