<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Tokenizer;

use Spiral\Core\Component;
use Spiral\Core\Container\InjectorInterface;
use Spiral\Core\Container\SingletonInterface;
use Spiral\Core\Exceptions\Container\InjectionException;
use Spiral\Core\MemoryInterface;
use Spiral\Core\NullMemory;
use Spiral\Debug\Traits\BenchmarkTrait;
use Spiral\Debug\Traits\LoggerTrait;
use Spiral\Files\FileManager;
use Spiral\Files\FilesInterface;
use Spiral\Tokenizer\Configs\TokenizerConfig;
use Spiral\Tokenizer\Reflections\ReflectionFile;
use Spiral\Tokenizer\Traits\TokensTrait;
use Symfony\Component\Finder\Finder;

/**
 * Default implementation of spiral tokenizer support while and blacklisted directories and etc.
 *
 * @todo this component have been written long time ago and require facelift
 */
class Tokenizer extends Component implements SingletonInterface, TokenizerInterface, InjectorInterface
{
    use LoggerTrait, BenchmarkTrait, TokensTrait;

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
     *
     * @var FilesInterface
     */
    protected $files;

    /**
     * @invisible
     *
     * @var MemoryInterface
     */
    protected $memory;

    /**
     * Tokenizer constructor.
     *
     * @param TokenizerConfig $config
     * @param FilesInterface  $files
     * @param MemoryInterface $memory
     */
    public function __construct(
        TokenizerConfig $config,
        FilesInterface $files = null,
        MemoryInterface $memory = null
    ) {
        $this->config = $config;

        $this->files = $files ?? new FileManager();
        $this->memory = $memory ?? new NullMemory();
    }

    /**
     * {@inheritdoc}
     */
    public function fileReflection(string $filename): ReflectionFile
    {
        $fileMD5 = $this->files->md5($filename = $this->files->normalizePath($filename));

        $reflection = new ReflectionFile(
            $filename,
            $this->normalizeTokens(token_get_all($this->files->read($filename))),
            (array)$this->memory->loadData(self::MEMORY . '.' . $fileMD5)
        );

        //Let's save to cache
        $this->memory->saveData(self::MEMORY . '.' . $fileMD5, $reflection->exportSchema());

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
        return new InvocationsLocator($this, $this->makeFinder($directories, $exclude));
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
     * @param array $directories
     * @param array $exclude
     *
     * @return Finder
     */
    private function makeFinder(
        array $directories = [],
        array $exclude = []
    ): Finder {
        $finder = new Finder();

        if (empty($directories)) {
            $directories = $this->config->getDirectories();
        }

        if (empty($exclude)) {
            $exclude = $this->config->getExcludes();
        }

        return $finder->files()->in($directories)->exclude($exclude)->name('*.php');
    }
}
