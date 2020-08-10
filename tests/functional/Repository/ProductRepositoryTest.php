<?php

namespace App\Tests\Functional\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductRepositoryTest extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function testGetProducts()
    {
        $paginator = $this
            ->productRepository
            ->getProducts(1, 3);

        $this->assertInstanceOf(Paginator::class, $paginator);
        $this->assertEquals(6, $paginator->count());

        foreach ($paginator as $item) {
            $this->assertInstanceOf(Product::class, $item);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->entityManager = self::bootKernel()->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->productRepository = $this->entityManager->getRepository(Product::class);
    }
}
