<?php

namespace Sff\PubliciteBundle\Entity;

/**
 * PubliciteRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PubliciteRepository extends \Doctrine\ORM\EntityRepository
{
    public function getPublicite() {
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder->where('p.nbClic < p.maxClic')
            ->addSelect('RAND() as HIDDEN rand')
            ->addOrderBy('rand')
            ->andWhere('p.nbAffichage < p.maxAffichage')
            ->andWhere('p.dateDebut < :now')
            ->andWhere('p.dateFin > :now')
            ->setMaxResults(1)
            ->setParameter('now', new \DateTime());

        $publicite = $queryBuilder->getQuery()->getOneOrNullResult();
        if($publicite != null) {
            $publicite->affiche();
            $this->_em->flush($publicite);
        }
        return $publicite;
    }
}
