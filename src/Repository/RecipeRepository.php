<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recipe[]    findAll()
 * @method Recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    /**
     * @return Recipe[] Returns an array of the 3 best recipes objects order by the best average_rate
     */
    public function findBestRecipes()
    {
        $qb =  $this->createQueryBuilder('r');
        $qb
            ->orderBy('r.averageRate', 'DESC')
            ->setMaxResults(6)
        ;
        return $qb->getQuery()->getResult();
    }

    public function findLatestRecipes()
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param string|null $term
     * @return Recipe[] Returns an array of result for the term write in the search bar
     */
    public function findAllWithSearch(?string $term)
    {
        $qb = $this->createQueryBuilder('r');
        if ($term) {
            $qb
                ->addSelect('u')
                ->leftJoin('r.user', 'u')
                ->andWhere('r.name LIKE :term OR r.content LIKE :term OR r.ingredient LIKE :term OR u.username LIKE :term')
                ->setParameter('term', '%' . $term . '%')
                ;
        }
        return $qb
            ->orderBy('r.name', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param int|null $categoryId
     * @return Recipe[] Returns an array of result for the term write in the search bar
     */
    public function findByCategory(?int $categoryId)
    {
        $qb = $this->createQueryBuilder('r');
        if ($categoryId) {
            $qb
                ->addSelect('c, sc, t')
                ->leftJoin('r.type', 't')
                ->leftJoin('t.subCategory', 'sc')
                ->leftJoin('sc.category', 'c')
                ->where('c.id = :categoryId')
                ->setParameter('categoryId', $categoryId)
                ;
        }
        return $qb
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param int|null $subCategoryId
     * @return Recipe[] Returns an array of result for the term write in the search bar
     */
    public function findBySubCategory(?int $subCategoryId)
    {
        $qb = $this->createQueryBuilder('r');
        if ($subCategoryId) {
            $qb
                ->addSelect('sc, t')
                ->leftJoin('r.type', 't')
                ->leftJoin('t.subCategory', 'sc')
                ->where('sc.id = :subCategoryId')
                ->setParameter('subCategoryId', $subCategoryId)
                ;
        }
        return $qb
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

        /**
     * @param int|null $typeId
     * @return Recipe[] Returns an array of result for the term write in the search bar
     */
    public function findByType (?int $typeId)
    {
        $qb = $this->createQueryBuilder('r');
        if ($typeId) {
            $qb
                ->addSelect('t')
                ->leftJoin('r.type', 't')
                ->where('t.id = :typeId')
                ->setParameter('typeId', $typeId)
                ;
        }
        return $qb
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
}
