<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Tokenizer\Highlighter;

/**
 * Highlight code tokens. Attention, you have to specify container and container colors manually.
 */
class Style
{
    /**
     * Style templates.
     *
     * @var array
     */
    protected $templates = [
        'token'       => '<span style="{style}">{code}</span>',
        'line'        => "<div><span class=\"number\">{number}</span>{code}</div>\n",
        'highlighted' => "<div class=\"highlighted\"><span class=\"number\">{number}</span>{code}</div>\n",
    ];

    /**
     * Styles associated with token types.
     *
     * @var array
     */
    protected $styles = [
        'color: blue; font-weight: bold;'   => [
            T_STATIC,
            T_PUBLIC,
            T_PRIVATE,
            T_PROTECTED,
            T_CLASS,
            T_NEW,
            T_FINAL,
            T_ABSTRACT,
            T_IMPLEMENTS,
            T_CONST,
            T_ECHO,
            T_CASE,
            T_FUNCTION,
            T_GOTO,
            T_INCLUDE,
            T_INCLUDE_ONCE,
            T_REQUIRE,
            T_REQUIRE_ONCE,
            T_VAR,
            T_INSTANCEOF,
            T_INTERFACE,
            T_THROW,
            T_ARRAY,
            T_IF,
            T_ELSE,
            T_ELSEIF,
            T_TRY,
            T_CATCH,
            T_CLONE,
            T_WHILE,
            T_FOR,
            T_DO,
            T_UNSET,
            T_FOREACH,
            T_RETURN,
            T_EXIT,
        ],
        'color: blue'                       => [
            T_DNUMBER,
            T_LNUMBER,
        ],
        'color: black; font: weight: bold;' => [
            T_OPEN_TAG,
            T_CLOSE_TAG,
            T_OPEN_TAG_WITH_ECHO,
        ],
        'color: gray;'                      => [
            T_COMMENT,
            T_DOC_COMMENT,
        ],
        'color: green; font-weight: bold;'  => [
            T_CONSTANT_ENCAPSED_STRING,
            T_ENCAPSED_AND_WHITESPACE,
        ],
        'color: #660000;'                   => [
            T_VARIABLE,
        ],
    ];

    /**
     * Highlight given token.
     *
     * @param int    $token PHP token type used to correctly resolve proper color.
     * @param string $code  Token source code.
     *
     * @return string
     */
    public function highlightToken($token, string $code): string
    {
        foreach ($this->styles as $style => $tokens) {
            if (!in_array($token, $tokens)) {
                //Nothing to highlight
                continue;
            }

            if (strpos($code, "\n") === false) {
                return \Spiral\interpolate($this->templates['token'], compact('style', 'code'));
            }

            $lines = [];
            foreach (explode("\n", $code) as $line) {
                $lines[] = \Spiral\interpolate($this->templates['token'], [
                    'style' => $style,
                    'code'  => $line,
                ]);
            }

            return implode("\n", $lines);
        }

        return $code;
    }

    /**
     * Highlight one line of code.
     *
     * @param int    $number        Line number in file.
     * @param string $code          Line code.
     * @param bool   $highlightLine Indication that line must be highlighted (i.e. current line).
     *
     * @return string
     */
    public function line(int $number, string $code, bool $highlightLine = false): string
    {
        return \Spiral\interpolate(
            $this->templates[$highlightLine ? 'highlighted' : 'line'],
            compact('number', 'code')
        );
    }
}
