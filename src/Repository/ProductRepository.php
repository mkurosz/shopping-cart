<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Get paginated products listing.
     *
     * @param int $page
     * @param int $itemsPerPage
     *
     * @return Paginator
     */
    public function getProducts(int $page, int $itemsPerPage): Paginator
    {
        return new Paginator(
            $this->createQueryBuilder('p')
                ->orderBy('p.id', 'ASC')
                ->setFirstResult($itemsPerPage * ($page - 1))
                ->setMaxResults($itemsPerPage)
                ->getQuery()
        );
    }
}
