<?php

namespace App\Repository;

use App\Controller\Queries\HomepageSearchData;
use App\Entity\JobListing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JobListing>
 *
 * @method JobListing|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobListing|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobListing[]    findAll()
 * @method JobListing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobListingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobListing::class);
    }

    public function save(JobListing $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(JobListing $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return JobListing[] Returns an array of JobListing objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('j.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?JobListing
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function searchJobListings(HomepageSearchData $searchData): array
    {
        $query = $this->createQueryBuilder('j')
            ->where('j.title LIKE :keywords')
            ->setParameter('keywords', '%'.$searchData->getTitle().'%');

        if ($searchData->getTitle()) {
            $query->andWhere('j.title = :title ')
                ->setParameter('title', $searchData->getTitle());
        }

        return $query->getQuery()->getResult();
    }

}
