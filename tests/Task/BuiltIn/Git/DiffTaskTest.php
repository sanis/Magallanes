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
            "echo \"src/Runtime/Runtime.php\nsrc/Task/BuiltIn/Composer/DumpAutoloadTask.php\nsrc/Task/BuiltIn/Composer/InstallTask.php\nsrc/Task/BuiltIn/Symfony/AsseticDumpTask.php\nsrc/Task/BuiltIn/Symfony/AssetsInstallTask.php\nsrc/Task/BuiltIn/Symfony/CacheClearTask.php\nsrc/Task/BuiltIn/Symfony/CacheWarmupTask.php\ntests/Command/BuiltIn/DeployCommandMiscTasksTest.php\ntests/Resources/composer-env.yml\" >> ./.mage-deployment/diff_changed.txt",
            $runtime->getRanCommands()[3]
        );
        $this->assertEquals(
            "echo \"\" >> ./.mage-deployment/diff_deleted.txt",
            $runtime->getRanCommands()[4]
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
