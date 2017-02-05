<?php
/**
 * Created by PhpStorm.
 * User: jbolys
 * Date: 02/02/2017
 * Time: 17:45
 */

namespace Mage\Task\BuiltIn\Git;


use Mage\Task\AbstractTask;
use Mage\Task\Exception\ErrorException;
use Symfony\Component\Process\Process;

class DiffTask extends AbstractTask
{
    public function getName()
    {
        return 'git/extract-diff';
    }

    public function getDescription()
    {
        $options = $this->getOptions();
        $from    = $options['from'];
        $to      = $options['to'];

        return sprintf('[Git] Export changes from (%s) to (%s)', $from, $to);
    }

    public function execute()
    {
        $deployPath = './.mage-deployment';
        $this->runtime->runLocalCommand('rm -rf '.$deployPath);
        $this->runtime->runLocalCommand('mkdir '.$deployPath);

        $options = $this->getOptions();

        $cmdGetDiff = sprintf('%s diff %s %s --name-status', $options['path'], $options['from'], $options['to']);

        /** @var Process $process */
        $process = $this->runtime->runLocalCommand($cmdGetDiff);
        if ($process->isSuccessful()) {
            $diffOutput = $process->getOutput();
            preg_match_all('/(?\'change\'\w)\s+(?\'file\'.+)/m', $diffOutput, $matches, PREG_SET_ORDER);
            $diff = ['changed' => [], 'deleted' => []];
            foreach ($matches as $match) {
                if ($match['change'] == 'D') {
                    $diff['deleted'][]= $match['file'];
                } else {
                    $diff['changed'][] = $match['file'];
                }
            }

            $diffChanged = implode("\n", $diff['changed']);
            $diffDeleted = implode("\n", $diff['deleted']);
            $this->runtime->runLocalCommand("echo \"{$diffChanged}\" >> {$deployPath}/diff_changed.txt");
            $this->runtime->runLocalCommand("echo \"{$diffDeleted}\" >> {$deployPath}/diff_deleted.txt");
        }

        return $process->isSuccessful();
    }

    protected function getOptions()
    {

        $from = $this->runtime->getEnvOption('from', '');
        $to   = $this->runtime->getEnvOption('to', 'HEAD');

        $options = array_merge(
            ['path' => 'git', 'from' => $from, 'to' => $to],
            $this->options
        );

        if (!$options['from']) {
            throw new ErrorException('Missing FROM parameter');
        }

        return $options;
    }

}
