<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Tokenizer\Highlighter;

/**
 * Dark highlighter.
 */
class InversedStyle extends Style
{
    /**
     * Styles associated with token types.
     *
     * @var array
     */
    protected $styles = [
        'color: #C26230; font-weight: bold;' => [
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
            T_EXTENDS,
        ],
        'color: black; font: weight: bold;'  => [
            T_OPEN_TAG,
            T_CLOSE_TAG,
            T_OPEN_TAG_WITH_ECHO,
        ],
        'color: #BC9458;'                    => [
            T_COMMENT,
            T_DOC_COMMENT,
        ],
        'color: #A5C261;'                    => [
            T_CONSTANT_ENCAPSED_STRING,
            T_ENCAPSED_AND_WHITESPACE,
            T_DNUMBER,
            T_LNUMBER,
        ],
        'color: #D0D0FF;'                    => [
            T_VARIABLE,
        ],
    ];
}
