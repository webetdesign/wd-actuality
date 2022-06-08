<?php

namespace WebEtDesign\ActualityBundle\Repository;

use Doctrine\ORM\NonUniqueResultException;
use WebEtDesign\ActualityBundle\Entity\WDActualityTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebEtDesign\ActualityBundle\Entity\WDCategoryTranslation;

/**
 * @extends ServiceEntityRepository<WDCategoryTranslation>
 *
 * @method WDCategoryTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method WDCategoryTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method WDCategoryTranslation[]    findAll()
 * @method WDCategoryTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WDCategoryTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WDCategoryTranslation::class);
    }

    public function add(WDCategoryTranslation $entity, bool $flush = false): void
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
}
