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
     * Packagist package name
     */
    private const PACKAGE_NAME = 'ashleydawson/deskpro-hab';

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
        $updater = new Updater(null, false);
        $updater->setStrategy(Updater::STRATEGY_GITHUB);

        $updater->getStrategy()->setPackageName(self::PACKAGE_NAME);
        $updater->getStrategy()->setPharName('hab.phar');
        $updater->getStrategy()->setCurrentLocalVersion('@git_tag@'); // Replaced by box phar compilation

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
