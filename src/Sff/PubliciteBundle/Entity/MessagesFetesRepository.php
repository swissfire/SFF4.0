<?php

namespace Sff\PubliciteBundle\Entity;

/**
 * MessagesFetesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MessagesFetesRepository extends \Doctrine\ORM\EntityRepository
{
    public function getCurrentMessage() {
        $queryBuilder = $this->createQueryBuilder('m');
        $queryBuilder->where('m.dateDebut < :now')
            ->andWhere('m.dateFin > :now')
            ->setParameter('now', new \DateTime());
        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}
