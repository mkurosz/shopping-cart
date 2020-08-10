<?php

namespace App\Tests\Functional\Service;

use App\Dto\ProductInput;
use App\Entity\Product;
use App\ReadModel\ListingResult;
use App\Repository\ProductRepository;
use App\Service\ProductCrudManager;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductCrudManagerTest extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductCrudManager
     */
    private $productCrudManager;

    public function testPersistNewEntity()
    {
        $product = new Product(
            'Forrest Gump',
            6999,
            'PLN'
        );

        $this->productCrudManager->persistEntity($product);

        $newlyAddedProduct = $this->productRepository->findOneBy(['name' => 'Forrest Gump']);

        $this->assertInstanceOf(Product::class, $newlyAddedProduct);
        $this->assertIsInt($newlyAddedProduct->getId());
        $this->assertEquals($product->getName(), $newlyAddedProduct->getName());
        $this->assertEquals($product->getPrice(), $newlyAddedProduct->getPrice());
        $this->assertEquals($product->getCurrencyCode(), $newlyAddedProduct->getCurrencyCode());
    }

    public function testPersistExistingEntity()
    {
        $product = $this->productRepository->findOneBy(['name' => 'Steve Jobs']);
        $product->setPrice(9999);

        $this->productCrudManager->persistEntity($product);

        $existingProduct = $this->productRepository->findOneBy(['name' => 'Steve Jobs']);

        $this->assertEquals($product->getPrice(), $existingProduct->getPrice());
    }

    public function testRemoveEntity()
    {
        $this->productCrudManager->removeEntity($this->productRepository->findOneBy(['name' => 'Steve Jobs']));

        $this->assertNull($this->productRepository->findOneBy(['name' => 'Steve Jobs']));
    }

    public function testGetProducts()
    {
        $result = $this->productCrudManager->getProducts(1, 3);

        $this->assertInstanceOf(ListingResult::class, $result);
        $this->assertEquals(3, count($result->getItems()));
        $this->assertEquals(3, $result->getItemsPerPage());
        $this->assertEquals(2, $result->getPagesCount());
        $this->assertEquals(6, $result->getTotalCount());

        $this->productCrudManager->createFromDto(
            new ProductInput(
                'Forrest Gump',
                6999,
                'PLN'
            )
        );

        $result = $this->productCrudManager->getProducts(1, 3);

        $this->assertInstanceOf(ListingResult::class, $result);
        $this->assertEquals(3, count($result->getItems()));
        $this->assertEquals(3, $result->getItemsPerPage());
        $this->assertEquals(3, $result->getPagesCount());
        $this->assertEquals(7, $result->getTotalCount());
    }

    public function testCreateFromDto()
    {
        $productInput = new ProductInput(
            'Forrest Gump',
            6999,
            'PLN'
        );
        $result = $this->productCrudManager->createFromDto($productInput);

        $this->assertInstanceOf(Product::class, $result);
        $this->assertIsInt($result->getId());
        $this->assertEquals($productInput->getName(), $result->getName());
        $this->assertEquals($productInput->getPrice(), $result->getPrice());
        $this->assertEquals($productInput->getCurrencyCode(), $result->getCurrencyCode());
    }

    public function testUpdateFromDto()
    {
        $product = $this->productRepository->findOneBy(['name' => 'Steve Jobs']);
        $productId = $product->getId();
        $productInput = new ProductInput(
            'Forrest Gump',
            6999,
            'PLN'
        );
        $result = $this->productCrudManager->updateFromDto($product, $productInput);

        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals($productId, $result->getId());
        $this->assertEquals($productInput->getName(), $result->getName());
        $this->assertEquals($productInput->getPrice(), $result->getPrice());
        $this->assertEquals($productInput->getCurrencyCode(), $result->getCurrencyCode());
    }

    public function testDelete()
    {
        $this->productCrudManager->delete($this->productRepository->findOneBy(['name' => 'Steve Jobs']));

        $this->assertNull($this->productRepository->findOneBy(['name' => 'Steve Jobs']));
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->entityManager = self::bootKernel()->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->productRepository = $this->entityManager->getRepository(Product::class);
        $this->productCrudManager = new ProductCrudManager(
            $this->entityManager,
            $this->logger,
            $this->productRepository
        );
    }
}
