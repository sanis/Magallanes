<?php
/**
 * Created by PhpStorm.
 * User: jbolys
 * Date: 05/02/2017
 * Time: 10:29
 */

namespace Task\BuiltIn\Git;


use Mage\Runtime\Runtime;
use Mage\Task\BuiltIn\Git\DiffTask;
use Mage\Task\Exception\ErrorException;
use Mage\Tests\Runtime\RuntimeMockup;


class DiffTaskTest extends \PHPUnit_Framework_TestCase
{
    public function testDiffGet()
    {
        $runtime = new RuntimeMockup();
        $runtime->setConfiguration(['environments' => ['test' => []]]);
        $runtime->setEnvironment('test');

        $task = new DiffTask();
        $task->setOptions(
            ['from' => 'dc48f19b265150c4cc584cf6c0726d7cb78cef17', 'to' => '532a3146b07ea9eeeaf979e8b0228c4d0d4895d3']
        );
        $task->setRuntime($runtime);

        $this->assertContains('dc48f19b265150c4cc584cf6c0726d7cb78cef17', $task->getDescription());
        $this->assertContains('532a3146b07ea9eeeaf979e8b0228c4d0d4895d3', $task->getDescription());
        $task->execute();

        $this->assertEquals(
            [
                [
                    'change' => 'M',
                    'file'   => 'src/Runtime/Runtime.php',
                ],
                [
                    'change' => 'M',
                    'file'   => 'src/Task/BuiltIn/Composer/DumpAutoloadTask.php',
                ],
                [
                    'change' => 'M',
                    'file'   => 'src/Task/BuiltIn/Composer/InstallTask.php',
                ],
                [
                    'change' => 'M',
                    'file'   => 'src/Task/BuiltIn/Symfony/AsseticDumpTask.php',
                ],
                [
                    'change' => 'M',
                    'file'   => 'src/Task/BuiltIn/Symfony/AssetsInstallTask.php',
                ],
                [
                    'change' => 'M',
                    'file'   => 'src/Task/BuiltIn/Symfony/CacheClearTask.php',
                ],
                [
                    'change' => 'M',
                    'file'   => 'src/Task/BuiltIn/Symfony/CacheWarmupTask.php',
                ],
                [
                    'change' => 'M',
                    'file'   => 'tests/Command/BuiltIn/DeployCommandMiscTasksTest.php',
                ],
                [
                    'change' => 'A',
                    'file'   => 'tests/Resources/composer-env.yml',
                ],
            ],
            $task->getDiff()
        );
    }

    public function testMissingParameters()
    {
        $this->setExpectedException(ErrorException::class);

        $runtime = new Runtime();
        $runtime->setConfiguration(['environments' => ['test' => []]]);
        $runtime->setEnvironment('test');

        $task = new DiffTask();
        $task->setOptions(
            ['to' => '532a3146b07ea9eeeaf979e8b0228c4d0d4895d3']
        );
        $task->setRuntime($runtime);
        $task->execute();
    }
}
