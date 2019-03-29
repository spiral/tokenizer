<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Anton Titov (Wolfy-J)
 */

namespace Spiral\Tokenizer\Config;

use Spiral\Core\InjectableConfig;

/**
 * Tokenizer component configuration.
 */
class TokenizerConfig extends InjectableConfig
{
    const CONFIG = 'tokenizer';

    /**
     * @var array
     */
    protected $config = [
        'directories' => [],
        'exclude'     => [],
    ];

    /**
     * @return array
     */
    public function getDirectories(): array
    {
        return $this->config['directories'] ?? [getcwd()];
    }

    /**
     * @return array
     */
    public function getExcludes(): array
    {
        return $this->config['exclude'] ?? [];
    }
}
