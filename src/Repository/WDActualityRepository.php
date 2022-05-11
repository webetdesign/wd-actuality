<?php

namespace WebEtDesign\ActualityBundle\Repository;

use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use WebEtDesign\ActualityBundle\Entity\WDActuality;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use WebEtDesign\ActualityBundle\Entity\WDCategory;

/**
 * @method WDActuality|null find($id, $lockMode = null, $lockVersion = null)
 * @method WDActuality|null findOneBy(array $criteria, array $orderBy = null)
 * @method WDActuality[]    findAll()
 * @method WDActuality[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WDActualityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WDActuality::class);
    }

    public function findPublished()
    {
        $qb = $this->createQueryBuilder('a');
        $qb->where('a.published = 1')
            ->andWhere('a.publishedAt < :now')
            ->setParameter('now', new DateTime('now'))
            ->orderBy('a.publishedAt', 'DESC')
        ;

        return $qb;
    }

    public function findPublishedByCategory(WDCategory $category)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->where('a.published = 1')
            ->andWhere('a.publishedAt < :now')
            ->setParameter('now', new DateTime('now'))
            ->andWhere('a.category = :category')
            ->setParameter('category', $category)
            ->orderBy('a.publishedAt', 'DESC')
            ;

        return $qb;
    }

    public function findLast($limit = 3)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->where('a.published = 1')
            ->andWhere('a.publishedAt < :now')
            ->setParameter('now', new DateTime('now'))
            ->orderBy('a.publishedAt', 'DESC')
        ;

        $qb->setMaxResults($limit);

        if ($limit === 1) {
            return $qb->getQuery()->getOneOrNullResult();
        }

        return $qb->getQuery()->getResult();
    }

    // /**
    //  * @return Actuality[] Returns an array of Actuality objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Actuality
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
