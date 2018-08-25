<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Tokenizer\Tests;

use Spiral\Tokenizer\Isolator;

class IsolatorTest extends \PHPUnit_Framework_TestCase
{
    public function testDetection()
    {
        $source = '<?php echo 1, 2 ?> hello world <?= $var ?>';

        $isolator = new Isolator();
        $isolated = $isolator->isolatePHP($source);

        $this->assertNotEquals($source, $isolated);

        $this->assertContains('<?php echo 1, 2 ?>', $isolator->getBlocks());
        $this->assertContains('<?= $var ?>', $isolator->getBlocks());

        //Order
        $blocks = array_values($isolator->getBlocks());
        $this->assertEquals('<?php echo 1, 2 ?>', $blocks[0]);
        $this->assertEquals('<?= $var ?>', $blocks[1]);

        $restored = $isolator->repairPHP($isolated);
        $this->assertEquals($source, $restored);
    }

    public function testReplacement()
    {
        $source = '<?php echo 1, 2 ?> hello world <?= $var ?>';

        $isolator = new Isolator();
        $isolated = $isolator->isolatePHP($source);

        $keys = array_keys($isolator->getBlocks());
        $isolator->setBlock($keys[0], '<?php echo 2, 1 ?>');
        $isolator->setBlock($keys[1], '<?= e($var) ?>');

        $blocks = array_values($isolator->getBlocks());
        $this->assertEquals('<?php echo 2, 1 ?>', $blocks[0]);
        $this->assertEquals('<?= e($var) ?>', $blocks[1]);

        $restored = $isolator->repairPHP($isolated);

        $this->assertEquals('<?php echo 2, 1 ?> hello world <?= e($var) ?>', $restored);
    }

    /**
     * @expectedException \Spiral\Tokenizer\Exceptions\IsolatorException
     */
    public function testBadBlock()
    {
        $source = '<?php echo 1 ? > hello world <?= $var ?>';

        $isolator = new Isolator();
        $isolator->isolatePHP($source);
        $isolator->setBlock('abc', 'value');
    }


    public function testAttempt1()
    {
        $source = 'Hello, world... <?php echo "hack";';

        $isolator = new Isolator();
        $this->assertNotContains('<?', $isolator->isolatePHP($source));
    }

    public function testAttempt2()
    {
        $source = 'Hello, world... <?php ';

        $isolator = new Isolator();
        $this->assertNotContains('<?', $isolator->isolatePHP($source));
    }

    public function testAttempt3()
    {
        $source = 'Hello, world... <?=dump($_SERVER);';

        $isolator = new Isolator();
        $this->assertNotContains('<?', $isolator->isolatePHP($source));
    }
}