<?php
namespace ImproBundle\Repository;

use Doctrine\ORM\EntityRepository;

class WordRepository extends EntityRepository
{
    public function findRandomActive()
    {
        $sql = "SELECT w.id FROM word w LEFT JOIN blacklist b on b.word_id = w.id AND b.active = 1 WHERE w.id NOT IN (:word_ids) AND b.id IS NULL AND w.definition is not null GROUP BY 1 ORDER BY RANDOM() LIMIT 1";

        $ids = array();
        $types = array('word_ids' => \Doctrine\DBAL\Connection::PARAM_INT_ARRAY);

        for ($i = 0; $i <= 3; $i++) {
            $ids[] = $this->getEntityManager()->getConnection()->executeQuery($sql, array('word_ids' => $ids), $types)->fetchColumn();
        }

        return $this->findBy(array('id' => $ids));
    }

    public function findActive()
    {


        $dql = "SELECT w FROM ImproBundle:Word w LEFT JOIN w.blacklists b WITH b.active = true WHERE w.blacklists = null";

        return $this->getEntityManager()
            ->createQuery($dql)
            ->getResult();
    }
}
