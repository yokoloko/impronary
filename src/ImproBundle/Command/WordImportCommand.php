<?php

namespace ImproBundle\Command;

use ImproBundle\ImproBundle;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class WordImportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('word:import')
            ->setDescription('Import word into database')
            ->addArgument('resource', InputArgument::REQUIRED, 'Resource')
            ->addArgument('limit', InputArgument::OPTIONAL, 'Resource', 100);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $resource = $input->getArgument('resource');
        $limit = $input->getArgument('limit');

        if (!file_exists($resource)) {
            throw new \InvalidArgumentException("Resource $resource does not exists.");
        }

        $doctrine = $this->getContainer()->get('doctrine');

        $handle = fopen($resource, "r");
        if ($handle) {
            $i = 0;
            while ($i < $limit && ($line = fgets($handle)) !== false) {
                $escaped = preg_replace( "/\r|\n/", "", utf8_encode($line));
                $exists = $doctrine->getEntityManager()->getRepository('ImproBundle:Word')->findByName($escaped);

                if (count($exists) > 0) {
                    $output->writeln("Word $escaped already exists in database.");
                    continue;
                }

                $i++;

                $word = new \ImproBundle\Entity\Word($escaped);

                $doctrine->getEntityManager()->persist($word);

                $output->writeln("$escaped inserted.");
            }

            fclose($handle);

            $doctrine->getEntityManager()->flush();
        } else {
            throw new \InvalidArgumentException("Cannot open file : $resource.");
        }
    }
}