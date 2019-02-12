<?php

namespace GestionBundle\Repository;

/**
 * TransactionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TransactionRepository extends \Doctrine\ORM\EntityRepository
{
	public function sumInputByMonth($start,$end)
    {
        return $this->createQueryBuilder('r')
            ->select('SUM(r.amount) as sommeInput')
            ->where('r.createdAt BETWEEN :start AND :end')
            ->andWhere('r.isInput = :isInput')
            ->andWhere('r.isValid = :isValid')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('isInput', TRUE)
            ->setParameter('isValid', TRUE)
            ->getQuery()
            ->getSingleScalarResult();
            ;
    }

    public function sumOutputByMonth($start,$end)
    {
        return $this->createQueryBuilder('r')
            ->select('SUM(r.amount) as sommeInput')
            ->where('r.createdAt BETWEEN :start AND :end')
            ->andWhere('r.isInput = :isInput')
            ->andWhere('r.isValid = :isValid')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('isInput', FALSE)
            ->setParameter('isValid', TRUE)
            ->getQuery()
            ->getSingleScalarResult();
            ;
    }


	public function sumInputBeforeMonth($start)
    {
        return $this->createQueryBuilder('r')
            ->select('SUM(r.amount) as sommeInput')
            ->where('r.createdAt < :start ')
            ->andWhere('r.isInput = :isInput')
            ->andWhere('r.isValid = :isValid')
            ->setParameter('start', $start)
            ->setParameter('isInput', TRUE)
            ->setParameter('isValid', TRUE)
            ->getQuery()
            ->getSingleScalarResult();
            ;
    }

    public function sumOutputBeforeMonth($start)
    {
        return $this->createQueryBuilder('r')
            ->select('SUM(r.amount) as sommeInput')
            ->where('r.createdAt < :start ')
            ->andWhere('r.isInput = :isInput')
            ->andWhere('r.isValid = :isValid')
            ->setParameter('start', $start)
            ->setParameter('isInput', FALSE)
            ->setParameter('isValid', TRUE)
            ->getQuery()
            ->getSingleScalarResult();
            ;
    }

}
