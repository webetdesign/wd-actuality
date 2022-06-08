<?php

namespace App\Repository\Actuality;

use App\Entity\Actuality\Actuality;
use App\Entity\Actuality\Category;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use WebEtDesign\ActualityBundle\Entity\WDCategory;

/**
 * @extends ServiceEntityRepository<Actuality>
 *
 * @method Actuality|null find($id, $lockMode = null, $lockVersion = null)
 * @method Actuality|null findOneBy(array $criteria, array $orderBy = null)
 * @method Actuality[]    findAll()
 * @method Actuality[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActualityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Actuality::class);
    }

    public function findOneBySlug($slug, $locale)
    {
        try {
            return $this->createQueryBuilder('a')
                ->leftJoin('a.translations', 'tr')
                ->andWhere('tr.slug = :slug')
                ->andWhere('tr.locale = :locale')
                ->setParameter('slug', $slug)
                ->setParameter('locale', $locale)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return false;
        }
    }

    public function add(Actuality $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Actuality $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
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

    public function findPublishedByCategory(Category $category)
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
}
