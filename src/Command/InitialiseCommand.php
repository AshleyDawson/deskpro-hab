<?php

namespace DeskPRO\Hab\Command;

use DeskPRO\Hab\FilePathUtilsTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class InitialiseCommand
 *
 * @package DeskPRO\Hab\Command
 */
final class InitialiseCommand extends Command
{
    use FilePathUtilsTrait;

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('Initialise Hab for a given Deskpro project')
            ->addOption(
                'project-dir',
                null,
                InputOption::VALUE_OPTIONAL,
                'Deskpro project root directory',
                self::normaliseProjectDir(\Phar::running(false) ?: getcwd())
            )
            ->addOption(
                'hostname',
                null,
                InputOption::VALUE_OPTIONAL,
                'Hostname of the virtual machine',
                'deskpro.local'
            )
            ->addOption(
                'ip',
                null,
                InputOption::VALUE_OPTIONAL,
                'Private network IP address of the virtual machine',
                '10.40.1.23'
            )
            ->addOption(
                'memory',
                null,
                InputOption::VALUE_OPTIONAL,
                'Virtual machine memory in megabytes',
                4096
            )
            ->addOption(
                'cpus',
                null,
                InputOption::VALUE_OPTIONAL,
                'Number of virtual CPUs',
                2
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Force the creation of the necessary configuration files, overwriting existing ones'
            )
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectDir = self::normaliseProjectDir($input->getOption('project-dir'));
        $isForced = (bool) $input->getOption('force');

        $output->writeln('<fg=cyan>Initialising Deskpro Hab</>');

        // Create Vagrantfile
        if (! $this->hasVagrantFile($projectDir) || $isForced) {
            $this->createVagrantFile($projectDir);
            $output->writeln('<info> + Vagrantfile created</info>');
        } else {
            $output->writeln(' - Vagrantfile already exists, skipping...');
        }

        // Create hab.json
        if (! $this->hasHabFile($projectDir) || $isForced) {
            $this->createHabFile(
                $projectDir,
                $input->getOption('hostname'),
                $input->getOption('ip'),
                (int) $input->getOption('memory'),
                (int) $input->getOption('cpus')
            );
            $output->writeln('<info> + hab.json created</info>');
        } else {
            $output->writeln(' - hab.json already exists, skipping...');
        }

        // Create .hab directory
        if (! $this->hasHabDir($projectDir) || $isForced) {
            $this->createHabDir($projectDir);
            $output->writeln('<info> + .hab directory created</info>');
        } else {
            $output->writeln(' - .hab directory already exists, skipping...');
        }

        $output->writeln('<fg=cyan>Done.</>');

        $this->outputGitIgnoreDetails($output);
        $this->outputStartupDetails($output);
        $this->outputConnectionDetails($output, $input->getOption('hostname'));
    }

    /**
     * @param OutputInterface $output
     */
    private function outputStartupDetails(OutputInterface $output)
    {
        $output->writeln(PHP_EOL.'-> Run <fg=cyan>vagrant up && vagrant ssh</> to startup and '
            .'access the Deskpro Hab VM'.PHP_EOL);
    }

    /**
     * @param OutputInterface $output
     */
    private function outputGitIgnoreDetails(OutputInterface $output)
    {
        $output->writeln(PHP_EOL.'-> You should exclude the following from version control:'.PHP_EOL
            .PHP_EOL.implode(PHP_EOL, ['  /.vagrant', '  /.hab', '  /hab.json', '  /Vagrantfile']));
    }

    /**
     * @param OutputInterface $output
     * @param string $hostname
     */
    private function outputConnectionDetails(OutputInterface $output, string $hostname)
    {
        $table = new Table($output);
        $table
            ->setHeaderTitle('Connection Details')
            ->setHeaders(['Service', 'Details'])
            ->setRows([
                ['Application URL', sprintf('http://%s/', $hostname)],
                ['Elasticsearch URL', sprintf('http://%s:9200/', $hostname)],
                ['Database DSN', sprintf('mysql://root:root@%s:3306/deskpro', $hostname)],
            ])
        ;
        $table->render();
    }

    /**
     * @param string $projectDir
     * @return bool
     */
    private function hasVagrantFile(string $projectDir): bool
    {
        return self::filesystem()->exists($projectDir.self::createPath().'Vagrantfile');
    }

    /**
     * @param string $projectDir
     */
    private function createVagrantFile(string $projectDir)
    {
        self::filesystem()->copy(
            __DIR__.self::createPath(['..', 'Resources', 'boilerplate']).'Vagrantfile',
            $projectDir.self::createPath().'Vagrantfile',
            true
        );
    }

    /**
     * @param string $projectDir
     * @return bool
     */
    private function hasHabFile(string $projectDir): bool
    {
        return self::filesystem()->exists($projectDir.self::createPath().'hab.json');
    }

    /**
     * @param string $projectDir
     * @param string $hostname
     * @param string $ip
     * @param int $memory
     * @param int $cpus
     */
    private function createHabFile(string $projectDir, string $hostname, string $ip, int $memory, int $cpus)
    {
        self::filesystem()->dumpFile($projectDir.self::createPath().'hab.json', json_encode([
            'hostname' => $hostname,
            'ip' => $ip,
            'memory' => $memory < 1024
                ? 1024
                : $memory
            ,
            'cpus' => $cpus < 1
                ? 1
                : $cpus
            ,
        ], JSON_PRETTY_PRINT));
    }

    /**
     * @param string $projectDir
     * @return bool
     */
    private function hasHabDir(string $projectDir): bool
    {
        return self::filesystem()->exists($projectDir.self::createPath().'.hab');
    }

    /**
     * @param string $projectDir
     */
    private function createHabDir(string $projectDir)
    {
        self::filesystem()->remove($projectDir.self::createPath(['.hab']));

        $target = $projectDir.self::createPath(['.hab', 'provision']);

        self::filesystem()->mirror(
            __DIR__.self::createPath(['..', 'Resources', 'boilerplate', 'provision']),
            $target
        );
    }

    /**
     * @param string $projectDir
     * @return string
     */
    private static function normaliseProjectDir(string $projectDir): string
    {
        return realpath(
            is_dir($projectDir)
                ? rtrim($projectDir, DIRECTORY_SEPARATOR)
                : dirname($projectDir)
        );
    }

    /**
     * @return Filesystem
     */
    private static function filesystem(): Filesystem
    {
        return new Filesystem();
    }
}
