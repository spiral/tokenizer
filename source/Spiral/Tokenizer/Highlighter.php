<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Tokenizer;

use Spiral\Tokenizer\Highlighter\Style;
use Spiral\Tokenizer\Traits\TokensTrait;

/**
 * Highlights php file using specified style. For debug purposes only.
 */
class Highlighter
{
    use TokensTrait;

    /**
     * @invisible
     * @var Style
     */
    private $style = null;

    /**
     * @invisible
     * @var array
     */
    private $tokens = [];

    /**
     * Highlighted source.
     *
     * @var string
     */
    private $highlighted = '';

    /**
     * @param string     $source
     * @param Style|null $style
     */
    public function __construct($source = '', Style $style = null)
    {
        $this->style = $style ?? new Style();
        $this->tokens = $this->normalizeTokens(token_get_all($source));
    }

    /**
     * Get highlighter with different source. Immutable.
     *
     * @param string $source
     *
     * @return self
     */
    public function withSource(string $source): Highlighter
    {
        $highlighter = clone $this;
        $highlighter->tokens = $this->normalizeTokens(token_get_all($source));

        return $highlighter;
    }

    /**
     * Set highlighter Style. Immutable.
     *
     * @param Style $style
     *
     * @return self
     */
    public function withStyle(Style $style): Highlighter
    {
        $highlighter = clone $this;
        $highlighter->style = $style;

        return $highlighter;
    }

    /**
     * Get highlighted source.
     *
     * @return string
     */
    public function highlight(): string
    {
        if (!empty($this->highlighted)) {
            //Nothing to do
            return $this->highlighted;
        }

        $this->highlighted = '';
        foreach ($this->tokens as $tokenID => $token) {
            $this->highlighted .= $this->style->highlightToken(
                $token[TokenizerInterface::TYPE],
                htmlentities($token[TokenizerInterface::CODE])
            );
        }

        return $this->highlighted;
    }

    /**
     * Get only part of php file around specified line.
     *
     * @param int      $line   Set as null to avoid line highlighting.
     * @param int|null $around Set as null to return every line.
     *
     * @return string
     */
    public function lines(int $line, int $around = null): string
    {
        //Chinking by lines
        $lines = explode("\n", str_replace("\r\n", "\n", $this->highlight()));

        $result = "";
        foreach ($lines as $number => $code) {
            $human = $number + 1;
            if (
                !empty($around)
                && ($human < $line - $around || $human >= $line + $around)
            ) {
                //Not included in a range
                continue;
            }

            $result .= $this->style->line(
                $human,
                mb_convert_encoding($code, 'utf-8'),
                $human === $line
            );
        }

        return $result;
    }
}