<?php

namespace App\Tests\Functional\Service;

use App\Entity\Cart;
use App\Entity\Product;
use App\Service\CartCrudManager;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CartCrudManagerTest extends KernelTestCase
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
     * @var CartCrudManager
     */
    private $cartCrudManager;

    public function testPersistNewEntity()
    {
        $cart = new Cart();
        /** @var Product $product1 */
        $product1 = $this
            ->entityManager
            ->getRepository(Product::class)
            ->findOneBy(['name' => 'The Godfather']);
        /** @var Product $product2 */
        $product2 = $this
            ->entityManager
            ->getRepository(Product::class)
            ->findOneBy(['name' => 'Steve Jobs']);

        $cart->addProduct($product1);
        $cart->addProduct($product2);

        $this->cartCrudManager->persistEntity($cart);

        $newlyAddedCart = $this->getLastCart();

        $this->assertInstanceOf(Cart::class, $newlyAddedCart);
        $this->assertIsInt($newlyAddedCart->getId());
        $this->assertNotEmpty($newlyAddedCart->getCartItems());
        $this->assertTrue($newlyAddedCart->containsProduct($product1));
        $this->assertTrue($newlyAddedCart->containsProduct($product2));
        $this->assertEquals($product1->getPrice() + $product2->getPrice(), $newlyAddedCart->getTotalPrice());
    }

    public function testPersistExistingEntity()
    {
        $cart = $this->getFirstCart();
        $cartTotalPrice = $cart->getTotalPrice();
        /** @var Product $product */
        $product = $this
            ->entityManager
            ->getRepository(Product::class)
            ->findOneBy(['name' => 'Steve Jobs']);

        $cart->addProduct($product);

        $this->cartCrudManager->persistEntity($cart);

        $existingCart = $this->getFirstCart();

        $this->assertNotEmpty($existingCart->getCartItems());
        $this->assertTrue($existingCart->containsProduct($product));
        $this->assertEquals($cartTotalPrice + $product->getPrice(), $existingCart->getTotalPrice());
    }

    public function testPersistEntityFailed()
    {
        $this->expectException(RuntimeException::class);

        $cart = new Cart();
        /** @var Product $notExistingProduct */
        $notExistingProduct = $this
            ->getMockBuilder(Product::class)
            ->setConstructorArgs(['Not existing product', 199, 'PLN'])
            ->getMock();

        $cart->addProduct($notExistingProduct);

        $this->cartCrudManager->persistEntity($cart);
    }

    public function testRemoveEntity()
    {
        $cart = $this->getFirstCart();
        $cartId = $cart->getId();
        $this->cartCrudManager->removeEntity($cart);

        $this->assertNull($this->entityManager->getRepository(Cart::class)->find($cartId));
    }

    public function testCreate()
    {
        $result = $this->cartCrudManager->create();

        $this->assertInstanceOf(Cart::class, $result);
        $this->assertIsInt($result->getId());
        $this->assertEmpty($result->getCartItems());
    }

    public function testAddProduct()
    {
        $cart = $this->getFirstCart();
        $cartTotalPrice = $cart->getTotalPrice();
        /** @var Product $product */
        $product = $this
            ->entityManager
            ->getRepository(Product::class)
            ->findOneBy(['name' => 'The Godfather']);

        $result = $this->cartCrudManager->addProduct($cart, $product);

        $this->assertInstanceOf(Cart::class, $result);
        $this->assertEquals($cart->getId(), $result->getId());
        $this->assertTrue($result->containsProduct($product));
        $this->assertEquals($cartTotalPrice + $product->getPrice(), $result->getTotalPrice());
    }

    public function testRemoveProduct()
    {
        $cart = $this->getFirstCart();
        $cartTotalPrice = $cart->getTotalPrice();
        /** @var Product $product */
        $product = $this
            ->entityManager
            ->getRepository(Product::class)
            ->findOneBy(['name' => 'The Return of Sherlock Holmes']);

        $result = $this->cartCrudManager->removeProduct($cart, $product);

        $this->assertInstanceOf(Cart::class, $result);
        $this->assertEquals($cart->getId(), $result->getId());
        $this->assertFalse($result->containsProduct($product));
        $this->assertEquals($cartTotalPrice - $product->getPrice(), $result->getTotalPrice());
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
        $this->cartCrudManager = new CartCrudManager($this->entityManager, $this->logger);
    }

    /**
     * Get first cart.
     *
     * @return Cart
     */
    private function getFirstCart(): Cart
    {
        $carts = $this->entityManager->getRepository(Cart::class)->findBy([], ['id' => 'ASC']);

        return reset($carts);
    }

    /**
     * Get last cart.
     *
     * @return Cart
     */
    private function getLastCart(): Cart
    {
        $carts = $this->entityManager->getRepository(Cart::class)->findBy([], ['id' => 'ASC']);

        return end($carts);
    }
}
