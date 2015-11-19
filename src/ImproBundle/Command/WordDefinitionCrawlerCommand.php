<?php

namespace ImproBundle\Command;

use ImproBundle\Entity\Word;
use ImproBundle\Entity\Blacklist;
use ImproBundle\ImproBundle;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

class WordDefinitionCrawlerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('word:definition-crawler')
            ->setDescription('Import word definition into database')
            ->addArgument('limit', InputArgument::OPTIONAL, 'limit', 10);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $limit = $input->getArgument('limit');
        $repository = $words = $doctrine->getEntityManager()->getRepository('ImproBundle:Word');

        $words = $repository->findBy(array('definition' => null, 'retired' => false), array(), $limit);

        foreach ($words as $word) {
            $name = $word->getName();

            $html = file_get_contents('http://www.cnrtl.fr/lexicographie/' . urlencode($name));

            $crawler = new Crawler($html);

            $node = $crawler->filter('span[class="tlf_cdefinition"]');
            if ($node->count() == 0) {
                $output->writeln("Cannot find definition for word #" . $word->getId() . " : " . $name);

                $word->setRetired(true);
                $this->blacklist($word, "Aucune definition du mot trouvée.", $output);
                continue;
            }

            $definition   = strip_tags($node->html());
            $originalWord = utf8_encode(strtolower($crawler->filter('li[id="vitemselected"] span')->text()));

            if ($word->getName() != $originalWord) {
                $this->blacklist($word, "Ce mot existe sous une autre forme.", $output);
                $doctrine->getEntityManager()->persist($word);
            } else {
                $output->writeln("Do not blacklisting word " . $word->getName());
                $output->writeln("Do not blacklisting wordid " . $word->getId());
            }

            $crawler->filter('li[id="vitemselected"] span')->each(function ($crawler) {
                foreach ($crawler as $node) {
                    $node->parentNode->removeChild($node);
                }
            });

            $category = trim($crawler->filter('li[id="vitemselected"]')->text(), ', ');

            $word->setDefinition($definition);
            $word->setCategory($category);

            $output->writeln("$name : $definition.");
        }

        $output->writeln("FLUSH");
        $doctrine->getEntityManager()->flush();
    }

    public function blacklist(Word $word, $reason, OutputInterface $output)
    {
        $blacklist = new Blacklist($word, $reason);

        $doctrine = $this->getContainer()->get('doctrine');
        $doctrine->getEntityManager()->persist($blacklist);

        $word->addBlacklist($blacklist);
        $doctrine->getEntityManager()->persist($word);

        $output->writeln("Blacklisting word " . $word->getName());
    }
}