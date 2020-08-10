<?php

namespace App\Tests\Unit\Service;

use App\Entity\Cart;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\CartCrudManager;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RuntimeException;

class CartCrudManagerTest extends TestCase
{
    /**
     * @var EntityManager|MockObject
     */
    private $entityManager;

    /**
     * @var LoggerInterface|MockObject
     */
    private $logger;

    /**
     * @var ProductRepository|MockObject
     */
    private $productRepository;

    /**
     * @var CartCrudManager
     */
    private $cartCrudManager;

    public function testPersistNewEntity()
    {
        $cart = new Cart();
        /** @var Product|MockObject $product1 */
        $product1 = $this->createProductMock('The Godfather', 5999, 'PLN');
        /** @var Product|MockObject $product2 */
        $product2 = $this->createProductMock('Steve Jobs', 4995, 'PLN');

        $cart->addProduct($product1);
        $cart->addProduct($product2);

        $this->cartCrudManager->persistEntity($cart);

        $this->assertTrue(true);
    }

    public function testRemoveEntity()
    {
        $cart = $this->createMock(Cart::class);
        $this->cartCrudManager->removeEntity($cart);

        $this->assertTrue(true);
    }

    public function testAddProduct()
    {
        $cart = new Cart();
        $cartTotalPrice = $cart->getTotalPrice();
        /** @var Product|MockObject $product */
        $product = $this->createProductMock('The Godfather', 5999, 'PLN');

        $result = $this->cartCrudManager->addProduct($cart, $product);

        $this->assertInstanceOf(Cart::class, $result);
        $this->assertTrue($result->containsProduct($product));
        $this->assertEquals($cartTotalPrice + $product->getPrice(), $result->getTotalPrice());
    }

    public function testRemoveProduct()
    {
        $cart = new Cart();
        /** @var Product|MockObject $product */
        $product = $this->createProductMock('The Godfather', 5999, 'PLN');
        $cart->addProduct($product);
        $cartTotalPrice = $cart->getTotalPrice();

        $result = $this->cartCrudManager->removeProduct($cart, $product);

        $this->assertInstanceOf(Cart::class, $result);
        $this->assertFalse($result->containsProduct($product));
        $this->assertEquals($cartTotalPrice - $product->getPrice(), $result->getTotalPrice());
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->productRepository = $this->createMock(ProductRepository::class);

        $this->entityManager
            ->method('getRepository')
            ->with(Product::class)
            ->willReturn($this->productRepository);

        $this->cartCrudManager = new CartCrudManager($this->entityManager, $this->logger);
    }

    /**
     * Create product mock.
     *
     * @param string $name
     * @param int $price
     * @param string $currencyCode
     *
     * @return MockObject
     */
    private function createProductMock(string $name, int $price, string $currencyCode): MockObject
    {
        $product = $this->createMock(Product::class);
        $product
            ->method('getName')
            ->willReturn($name);
        $product
            ->method('getPrice')
            ->willReturn($price);
        $product
            ->method('getCurrencyCode')
            ->willReturn($currencyCode);

        return $product;
    }
}
