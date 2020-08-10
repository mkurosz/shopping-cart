<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Cart;
use App\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class CartTest.
 */
class CartTest extends TestCase
{
    /**
     * Test construct.
     */
    public function testConstruct(): void
    {
        $cart = new Cart();

        $this->assertInstanceOf(ArrayCollection::class, $cart->getCartItems());
        $this->assertEquals(new \DateTime(), $cart->getCreatedAt(), 'Invalid creation date', 10);
    }

    public function testAddProduct(): void
    {
        $cart = new Cart();
        $product1 = $this->createProductMock(123, 'Test name', 1234, 'AAA');
        $product2 = $this->createProductMock(234, 'Other name', 2345, 'BBB');

        $cart->addProduct($product1);

        $this->assertEquals(1, $cart->getCartItems()->count());
        $this->assertTrue($cart->containsProduct($product1));

        $cart->addProduct($product1);

        $this->assertEquals(1, $cart->getCartItems()->count());

        $cart->addProduct($product2);

        $this->assertEquals(2, $cart->getCartItems()->count());
        $this->assertTrue($cart->containsProduct($product2));
    }

    public function testRemoveProduct(): void
    {
        $cart = new Cart();
        $product1 = $this->createProductMock(123, 'Test name', 1234, 'AAA');
        $product2 = $this->createProductMock(234, 'Other name', 2345, 'BBB');

        $cart->addProduct($product1);
        $cart->addProduct($product2);

        $this->assertEquals(2, $cart->getCartItems()->count());
        $this->assertTrue($cart->containsProduct($product1));
        $this->assertTrue($cart->containsProduct($product2));

        $cart->removeProduct($product1);

        $this->assertEquals(1, $cart->getCartItems()->count());
        $this->assertFalse($cart->containsProduct($product1));
        $this->assertTrue($cart->containsProduct($product2));
    }

    public function testContainsProduct(): void
    {
        $cart = new Cart();
        $product1 = $this->createProductMock(123, 'Test name', 1234, 'AAA');
        $product2 = $this->createProductMock(234, 'Other name', 2345, 'BBB');

        $cart->addProduct($product1);

        $this->assertTrue($cart->containsProduct($product1));
        $this->assertFalse($cart->containsProduct($product2));
    }

    public function testIsAvailableToAddProduct(): void
    {
        $cart = new Cart();
        $product1 = $this->createProductMock(123, 'Test name', 1234, 'AAA');
        $product2 = $this->createProductMock(234, 'Other name', 2345, 'BBB');
        $product3 = $this->createProductMock(345, 'Other name', 2468, 'AAA');

        $cart->addProduct($product1);
        $cart->addProduct($product2);

        $this->assertTrue($cart->isAvailableToAddProduct());

        $cart->addProduct($product3);
        $this->assertFalse($cart->isAvailableToAddProduct());
    }

    public function testGetTotalPrice(): void
    {
        $cart = new Cart();
        $product1 = $this->createProductMock(123, 'Test name', 1234, 'AAA');
        $product2 = $this->createProductMock(234, 'Other name', 2345, 'BBB');
        $product3 = $this->createProductMock(345, 'Other name', 2468, 'AAA');

        $cart->addProduct($product1);
        $cart->addProduct($product2);
        $cart->addProduct($product3);

        $this->assertEquals(1234 + 2345 + 2468, $cart->getTotalPrice());
    }

    public function testGetTotalPriceFormatted(): void
    {
        $cart = new Cart();
        $product1 = $this->createProductMock(123, 'Test name', 1234, 'AAA');
        $product2 = $this->createProductMock(234, 'Other name', 2345, 'BBB');
        $product3 = $this->createProductMock(345, 'Other name', 2468, 'AAA');

        $cart->addProduct($product1);
        $cart->addProduct($product2);
        $cart->addProduct($product3);

        $this->assertEquals(
            number_format((1234 + 2345 + 2468) / 100, 2, ',', ''),
            $cart->getTotalPriceFormatted()
        );
    }
    /**
     * Create product mock.
     *
     * @param int $id
     * @param string $name
     * @param int $price
     * @param string $currencyCode
     *
     * @return MockObject
     */
    private function createProductMock(int $id, string $name, int $price, string $currencyCode): MockObject
    {
        $product = $this->createMock(Product::class);
        $product
            ->method('getId')
            ->willReturn($id);
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
