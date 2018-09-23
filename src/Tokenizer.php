<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Tokenizer;

use Spiral\Core\Container\InjectorInterface;
use Spiral\Core\Container\SingletonInterface;
use Spiral\Core\Exceptions\Container\InjectionException;
use Spiral\Core\MemoryInterface;
use Spiral\Core\NullMemory;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Reflection\ReflectionFile;
use Symfony\Component\Finder\Finder;

/**
 * Default implementation of spiral tokenizer support while and blacklisted directories and etc.
 * Current implementation is based on token parsing, AST support is desired in future.
 */
class Tokenizer implements SingletonInterface, TokenizerInterface, InjectorInterface
{
    /**
     * Memory section.
     */
    const MEMORY = 'tokenizer';

    /**
     * @var TokenizerConfig
     */
    protected $config;

    /**
     * @invisible
     * @var MemoryInterface
     */
    protected $memory;

    /**
     * Tokenizer constructor.
     *
     * @param TokenizerConfig $config
     * @param MemoryInterface $memory Caching.
     */
    public function __construct(TokenizerConfig $config, MemoryInterface $memory = null)
    {
        $this->config = $config;
        $this->memory = $memory ?? new NullMemory();
    }

    /**
     * {@inheritdoc}
     */
    public function fileReflection(string $filename): ReflectionFile
    {
        $fileID = sprintf(
            "%s/%s.%s",
            self::MEMORY,
            basename($filename),
            md5_file($filename)
        );

        $reflection = new ReflectionFile(
            $filename,
            $this->getTokens($filename),
            (array)$this->memory->loadData($fileID)
        );

        //Let's save to cache
        $this->memory->saveData($fileID, $reflection->exportSchema());

        return $reflection;
    }

    /**
     * Get pre-configured class locator.
     *
     * @param array $directories
     * @param array $exclude
     *
     * @return ClassesInterface
     */
    public function classLocator(
        array $directories = [],
        array $exclude = []
    ): ClassesInterface {
        return new ClassLocator($this, $this->makeFinder($directories, $exclude));
    }

    /**
     * Get pre-configured invocation locator.
     *
     * @param array $directories
     * @param array $exclude
     *
     * @return InvocationsInterface
     */
    public function invocationLocator(
        array $directories = [],
        array $exclude = []
    ): InvocationsInterface {
        return new InvocationLocator($this, $this->makeFinder($directories, $exclude));
    }

    /**
     * {@inheritdoc}
     *
     * @throws InjectionException
     */
    public function createInjection(\ReflectionClass $class, string $context = null)
    {
        if ($class->isSubclassOf(ClassesInterface::class)) {
            return $this->classLocator();
        } elseif ($class->isSubclassOf(InvocationsInterface::class)) {
            return $this->invocationLocator();
        }

        throw new InjectionException("Unable to create injection for {$class}");
    }

    /**
     * @param array $directories Overwrites default config values.
     * @param array $exclude     Overwrites default config values.
     *
     * @return Finder
     */
    private function makeFinder(array $directories = [], array $exclude = []): Finder
    {
        $finder = new Finder();

        if (empty($directories)) {
            $directories = $this->config->getDirectories();
        }

        if (empty($exclude)) {
            $exclude = $this->config->getExcludes();
        }

        return $finder->files()->in($directories)->exclude($exclude)->name('*.php');
    }

    /**
     * Get all tokes for specific file.
     *
     * @param string $filename
     *
     * @return array
     */
    private function getTokens(string $filename): array
    {
        $tokens = token_get_all(file_get_contents($filename));

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
