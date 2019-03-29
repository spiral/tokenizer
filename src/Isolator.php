<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Tokenizer;

use Spiral\Tokenizer\Exception\IsolatorException;

/**
 * Isolators used to find and replace php blocks in given src. Can be used by view processors,
 * or to remove php code from some string.
 */
class Isolator
{
    /**
     * Found PHP blocks to be replaced.
     *
     * @var array
     */
    private $phpBlocks = [];

    /**
     * Isolation prefix. Use any values that will not corrupt HTML or
     * other src.
     *
     * @var string
     */
    private $prefix;

    /**
     * Isolation postfix. Use any values that will not corrupt HTML
     * or other src.
     *
     * @var string
     */
    private $postfix;

    /**
     * @param string $prefix  Replaced block prefix, -php by default.
     * @param string $postfix Replaced block postfix, block- by default.
     */
    public function __construct(string $prefix = '-php-', string $postfix = '-block-')
    {
        $this->prefix = $prefix;
        $this->postfix = $postfix;
    }

    /**
     * Isolates all returned PHP blocks with a defined pattern. Method uses
     * token_get_all function. Resulted src have all php blocks replaced
     * with non executable placeholder.
     *
     * @param string $source
     *
     * @return string
     */
    public function isolatePHP(string $source): string
    {
        $phpBlock = false;

        $isolated = '';
        foreach (token_get_all($source) as $token) {
            if ($this->isOpenTag($token)) {
                $phpBlock = $token[1];

                continue;
            }

            if ($this->isCloseTag($token)) {
                $blockID = $this->uniqueID();

                $this->phpBlocks[$blockID] = $phpBlock . $token[1];
                $isolated .= $this->placeholder($blockID);

                $phpBlock = '';

                continue;
            }

            $tokenContent = is_array($token) ? $token[1] : $token;

            if (!empty($phpBlock)) {
                $phpBlock .= $tokenContent;
            } else {
                $isolated .= $tokenContent;
            }
        }

        return $isolated;
    }

    /**
     * Set block content by id.
     *
     * @param string $blockID
     * @param string $source
     *
     * @return self
     *
     * @throws IsolatorException
     */
    public function setBlock(string $blockID, string $source): Isolator
    {
        if (!isset($this->phpBlocks[$blockID])) {
            throw new IsolatorException("Undefined block {$blockID}");
        }

        $this->phpBlocks[$blockID] = $source;

        return $this;
    }

    /**
     * List of all found and replaced php blocks.
     *
     * @return array
     */
    public function getBlocks(): array
    {
        return $this->phpBlocks;
    }

    /**
     * Restore PHP blocks position in isolated src (isolatePHP() must
     * be already called).
     *
     * @param string $source
     * @param bool   $partial  Set to true to restore only some blocks (listed in a 3rd paramater).
     * @param array  $blockIDs Blocks to be restored when partial mode is on.
     *
     * @return string
     */
    public function repairPHP(string $source, bool $partial = false, array $blockIDs = []): string
    {
        return preg_replace_callback(
            $this->blockRegex(),
            function ($match) use ($partial, $blockIDs) {
                if ($partial && !in_array($match['id'], $blockIDs)) {
                    return $match[0];
                }

                if (!isset($this->phpBlocks[$match['id']])) {
                    return $match[0];
                }

                return $this->phpBlocks[$match['id']];
            },
            $source
        );
    }

    /**
     * Remove PHP blocks from isolated src (isolatePHP() must be
     * already called).
     *
     * @param string $isolatedSource
     *
     * @return string
     */
    public function removePHP(string $isolatedSource): string
    {
        return preg_replace($this->blockRegex(), '', $isolatedSource);
    }

    /**
     * Reset isolator state.
     */
    public function reset()
    {
        $this->phpBlocks = [];
    }

    /**
     * @return string
     */
    private function blockRegex(): string
    {
        return '/' . preg_quote($this->prefix) . '(?P<id>[0-9a-z]+)' . preg_quote($this->postfix) . '/';
    }

    /**
     * @return string
     */
    private function uniqueID(): string
    {
        return hash_hmac('md5', (string)count($this->phpBlocks), random_bytes(32));
    }

    /**
     * @param string $blockID
     *
     * @return string
     */
    private function placeholder(string $blockID): string
    {
        return $this->prefix . $blockID . $this->postfix;
    }

    /**
     * @param mixed $token
     *
     * @return bool
     */
    private function isOpenTag($token): bool
    {
        if (!is_array($token)) {
            return false;
        }

        if ($token[0] == T_ECHO && $token[1] == '<?=') {
            return true;
        }

        return $token[0] == T_OPEN_TAG || $token[0] == T_OPEN_TAG_WITH_ECHO;
    }

    /**
     * @param mixed $token
     *
     * @return bool
     */
    public function isCloseTag($token): bool
    {
        if (!is_array($token)) {
            return false;
        }

        return $token[0] == T_CLOSE_TAG;
    }
}
