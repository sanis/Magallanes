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
    private $diff = [];

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
        $options = $this->getOptions();

        $cmdGetDiff = sprintf('%s diff %s %s --name-status', $options['path'], $options['from'], $options['to']);

        /** @var Process $process */
        $process = $this->runtime->runLocalCommand($cmdGetDiff);
        if ($process->isSuccessful()) {
            $diffOutput = $process->getOutput();
            preg_match_all('/(?\'change\'\w)\s+(?\'file\'.+)/m', $diffOutput, $matches, PREG_SET_ORDER);
            $diff = [];
            foreach ($matches as $match) {
                $diff[] = ['change' => $match['change'], 'file' => $match['file']];
            }
            $this->diff = $diff;
        }

        return $process->isSuccessful();
    }

    /**
     * @return array
     */
    public function getDiff(): array
    {
        return $this->diff;
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
