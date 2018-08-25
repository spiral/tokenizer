<?php
/**
 * Spiral, Core Components
 *
 * @author Wolfy-J
 */

namespace Spiral\Tokenizer\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Spiral\Core\MemoryInterface;
use Spiral\Files\FileManager;
use Spiral\Tokenizer\Tests\Classes\ClassA;
use Spiral\Tokenizer\Tests\Classes\ClassB;
use Spiral\Tokenizer\Tests\Classes\ClassC;
use Spiral\Tokenizer\Tests\Classes\Inner\ClassD;
use Spiral\Tokenizer\Configs\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

class ClassLocatorTest extends TestCase
{
    public function testClassesAll()
    {
        $tokenizer = $this->getTokenizer();

        //Direct loading
        $classes = $tokenizer->classLocator()->getClasses();

        $this->assertArrayHasKey(self::class, $classes);
        $this->assertArrayHasKey(ClassA::class, $classes);
        $this->assertArrayHasKey(ClassB::class, $classes);
        $this->assertArrayHasKey(ClassC::class, $classes);
        $this->assertArrayHasKey(ClassD::class, $classes);

        //Excluded
        $this->assertArrayNotHasKey('Spiral\Tokenizer\Tests\Classes\Excluded\ClassXX', $classes);
        $this->assertArrayNotHasKey('Spiral\Tokenizer\Tests\Classes\Bad_Class', $classes);
    }

    public function testClassesByClass()
    {
        $tokenizer = $this->getTokenizer();

        //By namespace
        $classes = $tokenizer->classLocator()->getClasses(ClassD::class);

        $this->assertArrayHasKey(ClassD::class, $classes);

        $this->assertArrayNotHasKey(self::class, $classes);
        $this->assertArrayNotHasKey(ClassA::class, $classes);
        $this->assertArrayNotHasKey(ClassB::class, $classes);
        $this->assertArrayNotHasKey(ClassC::class, $classes);
    }

    public function testClassesByInterface()
    {
        $tokenizer = $this->getTokenizer();

        //By interface
        $classes = $tokenizer->classLocator()->getClasses('Spiral\Tokenizer\Tests\TestInterface');

        $this->assertArrayHasKey(ClassB::class, $classes);
        $this->assertArrayHasKey(ClassC::class, $classes);

        $this->assertArrayNotHasKey(self::class, $classes);
        $this->assertArrayNotHasKey(ClassA::class, $classes);
        $this->assertArrayNotHasKey(ClassD::class, $classes);
    }

    public function testClassesByTrait()
    {
        $tokenizer = $this->getTokenizer();

        //By trait
        $classes = $tokenizer->classLocator()->getClasses('Spiral\Tokenizer\Tests\TestTrait');

        $this->assertArrayHasKey(ClassB::class, $classes);
        $this->assertArrayHasKey(ClassC::class, $classes);

        $this->assertArrayNotHasKey(self::class, $classes);
        $this->assertArrayNotHasKey(ClassA::class, $classes);
        $this->assertArrayNotHasKey(ClassD::class, $classes);
    }

    public function testClassesByClassA()
    {
        $tokenizer = $this->getTokenizer();

        //By class
        $classes = $tokenizer->classLocator()->getClasses(ClassA::class);

        $this->assertArrayHasKey(ClassA::class, $classes);
        $this->assertArrayHasKey(ClassB::class, $classes);
        $this->assertArrayHasKey(ClassC::class, $classes);
        $this->assertArrayHasKey(ClassD::class, $classes);

        $this->assertArrayNotHasKey(self::class, $classes);
    }

    public function testClassesByClassB()
    {
        $tokenizer = $this->getTokenizer();

        //By class
        $classes = $tokenizer->classLocator()->getClasses(ClassB::class);

        $this->assertArrayHasKey(ClassB::class, $classes);
        $this->assertArrayHasKey(ClassC::class, $classes);

        $this->assertArrayNotHasKey(self::class, $classes);
        $this->assertArrayNotHasKey(ClassA::class, $classes);
        $this->assertArrayNotHasKey(ClassD::class, $classes);
    }

    protected function getTokenizer()
    {
        //Disabling cache
        $memory = m::mock(MemoryInterface::class);
        $memory->shouldReceive('loadData')->andReturn([]);
        $memory->shouldReceive('saveData');

        $config = m::mock(TokenizerConfig::class);

        $config->shouldReceive('getDirectories')->andReturn([__DIR__]);
        $config->shouldReceive('getExcludes')->andReturn(['Excluded']);

        $tokenizer = new Tokenizer($config, new FileManager(), $memory);

        return $tokenizer;
    }
}