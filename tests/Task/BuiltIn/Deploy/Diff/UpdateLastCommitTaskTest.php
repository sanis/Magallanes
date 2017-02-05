<?php
/**
 * Created by PhpStorm.
 * User: jbolys
 * Date: 05/02/2017
 * Time: 21:19
 */

namespace Mage\Task\BuiltIn\Deploy\Diff;


use Mage\Tests\Runtime\RuntimeMockup;

class UpdateLastCommitTaskTest extends \PHPUnit_Framework_TestCase
{
    public function testUpdateLastCommitTask()
    {
        $runtime = new RuntimeMockup();
        $runtime->setConfiguration(['environments' => ['test' => ['user' => 'tester']]]);
        $runtime->setEnvironment('test');

        $task = new UpdateLastCommitTask();
        $task->setRuntime($runtime);
        $this->assertContains('Updating last commit file', $task->getDescription());
        $task->execute();

        $this->assertEquals(
            "git rev-parse --verify HEAD",
            $runtime->getRanCommands()[0]
        );
        $this->assertEquals(
            'ssh -p 22 -q -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no tester@ sh -c \"echo \"NEWLASTCOMMITHASH\" >> .mage-deployment.log\"',
            $runtime->getRanCommands()[1]
        );
    }
}
