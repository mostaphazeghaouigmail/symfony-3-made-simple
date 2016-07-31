<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ClearApcCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('clearApc')
            ->setDescription('Clear Apc Cache')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $kernel = $this->getContainer()->get('kernel');
        $application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
        $application->setAutoExit(false);

        apc_clear_cache();
        apc_clear_cache('user');
        apc_clear_cache('opcode');

        $cacheDriver = new \Doctrine\Common\Cache\ArrayCache();
        $deleted = $cacheDriver->deleteAll();


        $output->writeln('All cache cleared.');
    }

}
