<?php

namespace DeskPRO\Hab\Command;

use DeskPRO\Hab\FilePathUtilsTrait;
use Humbug\SelfUpdate\Updater;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SelfUpdateCommand
 *
 * @package DeskPRO\Hab\Command
 */
class SelfUpdateCommand extends Command
{
    use FilePathUtilsTrait;

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('self-update')
            ->setDescription('Self update the hab.phar bootstrapper')
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $updater = new Updater();
        $updater->setStrategy(Updater::STRATEGY_GITHUB);

        $updater->getStrategy()->setPackageName('ashleydawson/deskpro-hab'); // fixme
        $updater->getStrategy()->setPharName('hab.phar');
        $updater->getStrategy()->setCurrentLocalVersion(
            file_get_contents(__DIR__.self::createPath(['..', '..']).'current.version')
        );

        try {
            if ($updater->update()) {
                $output->writeln('<info>Successfully updated!</info>');
            } else {
                $output->writeln('<info>No update available, you\'re currently using the latest version</info>');
            }
        } catch (\Exception $e) {
            $output->writeln('<error>Failed to update!</error>');
        }
    }
}
