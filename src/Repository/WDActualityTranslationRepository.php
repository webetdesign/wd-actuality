<?php

namespace WebEtDesign\ActualityBundle\Repository;

use WebEtDesign\ActualityBundle\Entity\WDActualityTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WDActualityTranslation>
 *
 * @method WDActualityTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method WDActualityTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method WDActualityTranslation[]    findAll()
 * @method WDActualityTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WDActualityTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WDActualityTranslation::class);
    }

    public function add(WDActualityTranslation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(WDActualityTranslation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return WDActualityTranslation[] Returns an array of WDActualityTranslation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('w.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?WDActualityTranslation
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
