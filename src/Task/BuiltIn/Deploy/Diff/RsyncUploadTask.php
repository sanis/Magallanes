<?php
namespace Mage\Task\BuiltIn\Deploy\Diff;

/**
 * Created by PhpStorm.
 * User: jbolys
 * Date: 05/02/2017
 * Time: 12:50
 */
class RsyncUploadTask extends \Mage\Task\AbstractTask
{
    public function getName()
    {
        return 'deploy/diff-upload';
    }

    public function getDescription()
    {
        return '[Deploy] Copying files with Rsync upload (DIFF only)';
    }

    public function execute()
    {
        $flags     = $this->runtime->getEnvOption('rsync', '-avz');
        $sshConfig = $this->runtime->getSSHConfig();
        $user      = $this->runtime->getEnvOption('user', $this->runtime->getCurrentUser());
        $host      = $this->runtime->getWorkingHost();
        $hostPath  = rtrim($this->runtime->getEnvOption('host_path'), '/');
        $targetDir = rtrim($hostPath, '/');

        if ($this->runtime->getEnvOption('releases', false)) {
            throw new ErrorException('Can\'t be used with Releases, use "deploy/tar/copy"');
        }

        if (file_exists('./.mage-deployment/diff_deleted.txt')) {
            $deletedFiles = file_get_contents('./.mage-deployment/diff_deleted.txt');
            $deletedFiles = explode("\n", $deletedFiles);

            foreach ($deletedFiles as $deletedFile) {
                $this->runtime->runRemoteCommand('rm -rf '.$targetDir.'/'.$deletedFile, false);
            }
        }

        $excludes = $this->getExcludes();
        $cmdRsync = sprintf(
            'rsync -e "ssh -p %d %s" %s %s --files-from=%s ./ %s@%s:%s',
            $sshConfig['port'],
            $sshConfig['flags'],
            $flags,
            $excludes,
            './.mage-deployment/diff_changed.txt',
            $user,
            $host,
            $targetDir
        );

        /** @var \Symfony\Component\Process\Process $process */
        $process = $this->runtime->runLocalCommand($cmdRsync, 600);

        return $process->isSuccessful();
    }

    protected function getExcludes()
    {
        $excludes = $this->runtime->getEnvOption('exclude', []);
        $excludes = array_merge(['.git'], $excludes);

        foreach ($excludes as &$exclude) {
            $exclude = '--exclude='.$exclude;
        }

        return implode(' ', $excludes);
    }
}
