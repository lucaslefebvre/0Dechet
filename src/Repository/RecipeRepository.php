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
     * @return Recipe[] Returns an array of all the recipes objects
     */
    public function findAllRecipes($sortBy)
    {
        $qb =  $this->createQueryBuilder('r');
        if ($sortBy == "rate") {
            $qb
            ->orderBy('r.averageRate', 'DESC')
            ;
        } else if ($sortBy == "difficultyDesc") {
            $qb
            ->orderBy('r.difficulty', 'DESC')
            ;
        } else if ($sortBy == "difficultyAsc") {
            $qb
            ->orderBy('r.difficulty', 'ASC')
            ;
        } else {
            $qb
            ->orderBy('r.createdAt', 'DESC')
            ;
        }
            return $qb->getQuery()->getResult();
    }

    /**
     * @return Recipe[] Returns an array of the 3 best recipes objects order by the best average_rate
     */
    public function findBestRecipes()
    {
        $qb =  $this->createQueryBuilder('r');
        $qb
            ->orderBy('r.averageRate', 'DESC')
            ->where('r.status = 1')
            ->setMaxResults(6)
        ;
        return $qb->getQuery()->getResult();
    }

    public function findLatestRecipes()
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.createdAt', 'DESC')
            ->where('r.status = 1')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param string|null $term
     * @return Recipe[] Returns an array of result for the term write in the search bar
     */
    public function findAllWithSearch(?string $term, $sortBy)
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
        if ($sortBy == "rate") {
            return $qb
            ->orderBy('r.averageRate', 'DESC')
            ->getQuery()
            ->getResult()
            ;
        } else if ($sortBy == "difficultyDesc") {
            return $qb
            ->orderBy('r.difficulty', 'DESC')
            ->getQuery()
            ->getResult()
            ;
        } else if ($sortBy == "difficultyAsc") {
            return $qb
            ->orderBy('r.difficulty', 'ASC')
            ->getQuery()
            ->getResult()
            ;
        } else {
            return $qb
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
            ;
        }
    }

    /**
     * @param int|null $categoryId
     * @return Recipe[] Returns an array of result for the term write in the search bar
     */
    public function findByCategory(?int $categoryId, $sortBy)
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
        if ($sortBy == "rate") {
            return $qb
            ->orderBy('r.averageRate', 'DESC')
            ->getQuery()
            ->getResult()
            ;
        } else if ($sortBy == "difficultyDesc") {
            return $qb
            ->orderBy('r.difficulty', 'DESC')
            ->getQuery()
            ->getResult()
            ;
        } else if ($sortBy == "difficultyAsc") {
            return $qb
            ->orderBy('r.difficulty', 'ASC')
            ->getQuery()
            ->getResult()
            ;
        } else {
            return $qb
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
            ;
        }
    }

    /**
     * @param int|null $subCategoryId
     * @return Recipe[] Returns an array of result for the term write in the search bar
     */
    public function findBySubCategory(?int $subCategoryId, $sortBy)
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
        if ($sortBy == "rate") {
            return $qb
            ->orderBy('r.averageRate', 'DESC')
            ->getQuery()
            ->getResult()
            ;
        } else if ($sortBy == "difficultyDesc") {
            return $qb
            ->orderBy('r.difficulty', 'DESC')
            ->getQuery()
            ->getResult()
            ;
        } else if ($sortBy == "difficultyAsc") {
            return $qb
            ->orderBy('r.difficulty', 'ASC')
            ->getQuery()
            ->getResult()
            ;
        } else {
            return $qb
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
            ;
        }
    }

        /**
     * @param int|null $typeId
     * @return Recipe[] Returns an array of result for the term write in the search bar
     */
    public function findByType (?int $typeId, $sortBy)
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
        if ($sortBy == "rate") {
            return $qb
            ->orderBy('r.averageRate', 'DESC')
            ->getQuery()
            ->getResult()
            ;
        } else if ($sortBy == "difficultyDesc") {
            return $qb
            ->orderBy('r.difficulty', 'DESC')
            ->getQuery()
            ->getResult()
            ;
        } else if ($sortBy == "difficultyAsc") {
            return $qb
            ->orderBy('r.difficulty', 'ASC')
            ->getQuery()
            ->getResult()
            ;
        } else {
            return $qb
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
            ;
        }
    }
}
