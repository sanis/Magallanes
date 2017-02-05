<?php
/**
 * Created by PhpStorm.
 * User: jbolys
 * Date: 05/02/2017
 * Time: 21:19
 */

namespace Mage\Task\BuiltIn\Deploy\Diff;


use Mage\Tests\Runtime\RuntimeMockup;

class LastCommitTaskTest extends \PHPUnit_Framework_TestCase
{
    public function testGettingLastCommit()
    {
        $runtime = new RuntimeMockup();
        $runtime->setConfiguration(['environments' => ['test' => ['user' => 'tester']]]);
        $runtime->setEnvironment('test');

        $task = new LastCommitTask();
        $task->setRuntime($runtime);
        $this->assertContains('Getting last commit hash from server', $task->getDescription());
        $task->execute();

        $this->assertEquals('LASTCOMMITHASH', $runtime->getEnvOption('from'));

        $this->assertEquals(
            "ssh -p 22 -q -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no tester@ sh -c \\\"cat .mage-deployment.log\\\"",
            $runtime->getRanCommands()[0]
        );
    }
}
