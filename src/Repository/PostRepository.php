<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function add(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function searchByPost(string $searchTerm): array
    {
        // on divise la recherche en mots-clés séparés par des espaces et on stocke dans $keywords
        $keywords = explode(' ', $searchTerm);

        $queryBuilder = $this->createQueryBuilder('ap');
        $queryBuilder
            ->join('ap.user', 'u')
            ->andWhere(
                $queryBuilder->expr()->andX(
                    // on utilise une boucle pour ajouter une condition "like" pour chaque mot-clé
                    ...array_map(
                        function ($index, $keyword) use ($queryBuilder) {
                            return $queryBuilder->expr()->like("ap.content", ":searchTerm$index")
                                . ' OR ' . $queryBuilder->expr()->like("ap.title", ":searchTerm$index")
                                . ' OR ' . $queryBuilder->expr()->like("u.username", ":searchTerm$index");
                        },
                        array_keys($keywords),
                        $keywords
                    )
                )
            )
            ->orderBy('ap.createdAt', 'DESC');
        // on définit les paramètres pour chaque mot-clé
        foreach ($keywords as $index => $keyword) {
            $queryBuilder->setParameter("searchTerm$index", '%' . $keyword . '%');
        }

        return $queryBuilder->getQuery()->getResult();
    }

//    /**
//     * @return Post[] Returns an array of Post objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Post
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
